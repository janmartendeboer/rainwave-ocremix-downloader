# Rainwave OC Remix Downloader

This project helps download songs from [OC ReMix](https://ocremix.org/) that are
favorited on [Rainwace.cc](https://rainwave.cc/ocremix/).

## Installation

Download the repository and install the Composer packages:

```
composer install
```

## Environment variables

To authenticate with the API, the following environment variables must be
available:

| Variable           | Description |
|:-------------------|:------------|
| `RAINWAVE_USER_ID` | The numeric user ID of your Rainwave.cc account. |
| `RAINWAVE_API_KEY` | The API key linked to the user ID. |

See the [Rainwave API Key Manager](https://rainwave.cc/keys/) to create and
retrieve API keys and user IDs.

One may use the file `.env` or `.env.local` to store the variables.

## Commands

The following is a list of commands made available through `bin/console`.

### Library sync

The library sync command allows the user to specify a path and then synchronize
all their favorite songs with the given library path.

```
bin/console library:sync <path>
```
