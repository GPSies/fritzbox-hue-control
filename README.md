# PHP: AVM FRITZ!Box and Philips Hue control

This simple PHP script helps to switch on / off plugs or lights via the Philips Hue Bridge by using a presence detection by wifi (especially AVM FRITZ!Box). I use it to control security cameras (Reolink 5MP PTZ) that are turned on via a Philips Hue plug when all family members have left our home.

## What you need
- AVM FRITZ!Box wifi router, e.g. 7590
- Philips Hue bridge
- at least one light or plug

## Installation

Use the [Philips developer site](https://developers.meethue.com/develop/get-started-2/) to create an API user. After that, install that script e.g. on a Raspberry PI or Synology and run it by a cronjob.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[MIT](https://choosealicense.com/licenses/mit/)
