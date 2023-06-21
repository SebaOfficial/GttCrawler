<?php
/**
 * Response Class.
 * This class provides methods for sending HTTP responses.
 * 
 * @package Gtt\Api\Components
 * @author Sebastiano Racca
*/
declare(strict_types=1);

namespace GTT\Api\Components;


final class Response{
    private int $httpCode;
    private array|object|null $body;


    /**
     * Response constructor.
     * Initialises the response with default values.
     */
    public function __construct(){
        $this->body = null;
        $this->httpCode = 204;
    }

    /**
     * Sets the HTTP Code.
     * 
     * @param int $code The code to be set.
     * 
     * @return self Returns the current instance.
     */
    public function setHttpCode(int $code): self{
        $this->httpCode = $code;
        return $this;
    }

    /**
     * Sets the body.
     * 
     * @param int $body The body to be set.
     * 
     * @return self Returns the current instance.
     */
    public function setBody(array|object $body): self{
        $this->body = $body;
        return $this;
    }

    /**
     * Sets response headers.
     * 
     * @param array A list of headers to be set.
     * 
     * @return self Returns the current instance.
     */
    public function setHeaders(array $headers): self{
        foreach($headers as $header)
            header($header, true);
        return $this;
    }

    /**
     * Sends an response to the HTTP Request.
     * 
     * @param bool $exit Wheter to exit the program or not.
     *                   Default is set to true.
     */
    public function send(bool $exit = true): void{
        http_response_code($this->httpCode);

        if(isset($this->body))
            echo json_encode($this->body);
        
        if($exit) exit;
    }


    /**
     * Sets the alowed HTTP Methods.
     * 
     * @param array $methods The allowed methods.
     */
    public function allowMethods(array $methods): self{
        header("Access-Control-Allow-Methods: " . implode(", ", $methods));
        return $this;
    }


    /**
     * Sets the Access-Control-Allow-Origin header.
     * 
     * @param array|null $origins The allowed origins.
     * 
     * @return bool True if the origin is allowed.
     *              False if the origin is not allowed.
     */
    public function allowOrigin(?array $origins): bool{
        
        if(empty($origins)){
            header("Access-Control-Allow-Origin: null");
            return false;
        }

        if(in_array("*", $origins)){
            header("Access-Control-Allow-Origin: *");
            return true;
        }

        if(!in_array($_SERVER['HTTP_ORIGIN'] ?? null, $origins))
            return false;

        header("Access-Control-Allow-Origin: " . ($_SERVER['HTTP_ORIGIN'] ?? "*"));
        return true;
    }


    /**
     * Sets the alowed Headers.
     * 
     * @param array $headers The allowed headers.
     */
    public function allowHeaders(?array $headers){
        header("Access-Control-Allow-Headers: " . implode(", ", $headers));   
    }
}


?>