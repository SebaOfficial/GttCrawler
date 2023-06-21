<?php
/**
 * GttClient Class.
 * This class provides methods for getting information about GTT's public transport.
 * 
 * @package Gtt\Api\Components
 * 
 * @author Roberto Guido <https://github.com/madbob>
 * @author Sebastiano Racca
 * 
*/
declare(strict_types=1);

namespace GTT\Api\Components;

use \GuzzleHttp\Client;
use \GTT\Api\Components\Database;
use \Carbon\Carbon;


final class GttClient{
    private int $stop;
    protected Database $db;

    public function __construct(int $stop){
        $this->stop = $stop;
        $this->db = new Database(\ROOT_DIR . "/gtt-crawler.db");
        $this->guzzleClient = new Client();
    }

    /**
     * Sends a POST request to the specified URL using GuzzleHTTP and returns the response as JSON.
     *
     * @param string $body The JSON body to send with the request.
     * @return object|null The response data.
     */
    private function getGTTData(string $body): ?object{
        $url = 'https://plan.muoversiatorino.it/otp/routers/mato/index/graphql';
    
        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $body,
        ]);
        
        return json_decode((string)$response->getBody());
    }


    /**
     * Retrieves the stop information and departure times.
     *
     * @return array Returns an array of departure information.
     */
    private function probeStop(): array{
        $request = '{
            "id": "q01",
            "query": "query StopRoutes($id_0:String!,$startTime_1:Long!,$timeRange_2:Int!,$numberOfDepartures_3:Int!) {stop(id:$id_0) {id,...F2}} fragment F0 on Alert {id,alertDescriptionText,alertHash,alertHeaderText,alertSeverityLevel,alertUrl,effectiveEndDate,effectiveStartDate,alertDescriptionTextTranslations {language,text},alertHeaderTextTranslations {language,text},alertUrlTranslations {language,text}} fragment F1 on Route {alerts {trip {pattern {code,id},id},id,...F0},id} fragment F2 on Stop {_stoptimesWithoutPatterns4nTcNn:stoptimesWithoutPatterns(startTime:$startTime_1,timeRange:$timeRange_2,numberOfDepartures:$numberOfDepartures_3,omitCanceled:false) {realtimeState,trip {pattern {code,id},route {gtfsId,shortName,longName,mode,color,id,...F1},id}},id}",
            "variables": {
                "id_0": "gtt:' . $this->stop . '",
                "startTime_1": ' . time() . ',
                "timeRange_2": 900,
                "numberOfDepartures_3": 100
            }
        }';
    
        $result = $this->getGTTData($request);
        $id = $result->data->stop->id ?? "";
    
        // Getting the data for the next 2 hours
        $offset = 60 * 60 * 2;
    
        // Querying the endpoint passing the stop ID
        $request = '{
            "id": "q02",
            "query": "query StopPageContentContainer_StopRelayQL($id_0:ID!,$startTime_1:Long!,$timeRange_2:Int!,$numberOfDepartures_3:Int!) {node(id:$id_0) {...F2}} fragment F0 on Route {alerts {alertSeverityLevel,effectiveEndDate,effectiveStartDate,trip {pattern {code,id},id},id},id} fragment F1 on Stoptime {realtimeState,realtimeDeparture,scheduledDeparture,realtimeArrival,scheduledArrival,realtime,trip {pattern {route {shortName,id,...F0},id},id}} fragment F2 on Stop {_stoptimesWithoutPatterns1WnWVl:stoptimesWithoutPatterns(startTime:$startTime_1,timeRange:$timeRange_2,numberOfDepartures:$numberOfDepartures_3,omitCanceled:false) {...F1},id}",
            "variables": {
                "id_0": "' . $id . '",
                "startTime_1": "' . time() . '",
                "timeRange_2": ' . $offset . ',
                "numberOfDepartures_3": 100
            }
        }';
    
        $result = $this->getGTTData($request);
        $ret = [];
    
        foreach($result->data->node as $prop => $data) {
            if (str_starts_with($prop, '_stoptimes')) {
                foreach($data as $row) {
                    $ret[] = (object) [
                        'line' => $row->trip->pattern->route->shortName,
                        'hour' => Carbon::today()->addSeconds($row->realtimeDeparture)->setTimezone('Europe/Rome')->format('H:i:s'),
                        'realtime' => ($row->realtimeState == 'UPDATED'),
                    ];
                }
            }
        }
    
        return $ret;
    }

    /**
     * Retrieves the stop information from the database or probes the stop if not found.
     *
     * @param int|null $stop The stop ID (optional). If not provided, uses the default stop.
     * @return object|null Returns the stop information object or null if not found.
     */
    public function askStop(?int $stop = NULL): ?object{
        $stop = $stop ?? $this->stop;

        return $this->db->execute(function($pdo) use($stop){

            $result = $pdo->query(
                sprintf("SELECT * FROM stops WHERE stop = %d AND strftime('%%s', 'now') - strftime('%%s', date) < 300", $stop)
            );

            $ret = [];

            while($r = $result->fetchObject()) {
                $ret[] = (object)[
                    'line' => $r->line,
                    'hour' => $r->hour,
                    'realtime' => $r->realtime,
                ];
            }

            if(empty($ret)){
                $fetch = $this->probeStop($stop);

                if($fetch == NULL){
                    return NULL;
                }

                $pdo->query(sprintf("DELETE FROM stops WHERE stop = %d", $stop));
                foreach($fetch as $f) {
                    $query = sprintf("INSERT INTO stops (stop, line, hour, realtime, date) VALUES (%d, %d, '%s', %d, datetime('now'))", $stop, $f->line, $f->hour, $f->realtime ? 1 : 0);
                    $pdo->query($query);
                }

                $ret = $fetch;
            }

            return (object)$ret;
            
        }, true);

    }
}