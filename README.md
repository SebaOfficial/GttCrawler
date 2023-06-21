<div align="center">
 	<img align="center" src="https://github-production-user-asset-6210df.s3.amazonaws.com/115643607/247591826-1d456de9-e612-468b-bf4e-99e290bf1eeb.png" alt="Gtt Crawler">
	<h1>GTT Api Client</h1>
</div>

Unofficial API to access information about the active public transport in Turin. 

# Installation
1. Clone this repository
```bash
git clone https://github.com/SebaOfficial/GttCrawler.git && cd GttCrawler
```

2. Install dependency
```bash
composer update
```

3. Either run your web server or use the PHP's Built-in web server<br>
```bash
php -S localhost:{your_port} -t src/public/
```
**:exclamation: Warning: Don't use the PHP's web server for production :exclamation:**

# Usage
You can either use the GUI in the [home page](https://gtt.racca.me/) or the provided API.

## API Usage
### GET /api.php
Get information about a stop.

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| stop      | int  | yes      | The Stop ID |

# Credits
* **Creator:** [Sebastiano Racca](https://racca.me)
* **Logo:** [OpenClipArt](https://openclipart.org/detail/260672/bus15)
* This is a **fork** of the [GTT Pirate API](https://github.com/madbob/gtt-pirate-api)

# License
GttCrawler is under the [GNU General Public](https://github.com/SebaOfficial/GttCrawler/blob/master/COPYING) License.