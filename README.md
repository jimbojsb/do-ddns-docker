# Digital Ocean DDNS Updater
This small Docker container will update a subdomain with the current public IP address of the computer / NAT network running the container at an interval of your choosing.

This container uses ifconfig.me as the source of its IP information.

### Usage
```bash
$ docker run -eSUBDOMAIN=home -eTLD=example.com -eAPI_KEY=[your api key] jimbojsb/digitalocean-ddns

```

**Options**
* SUBDOMAIN - the subdomain to update, this will be created if it does not exist
* TLD - the parent domain that you want to update, as listed in your DigitalOcean account
* API_KEY - A personal access token generated from your DigitalOcean account
* UPDATE_FREQUENCY - optional, internal to check in minutes
