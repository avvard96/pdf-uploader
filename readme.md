# PDF Uploader
## Description

PDF Uploader developed to upload PDF files from your Gmail inbox messages to Google Drive root folder.

## Installation

1. Clone repository
2. In project root folder run <b>composer install</b> command via terminal
3. Make sure <b>storage</b> and <b>bootstrap/cache</b> folders are writable
4. Duplicate <b>.env.example</b> file and rename it to <b>.env</b>
5. In project root folder run <b>php artisan key:generate</b> command via terminal.


## Usage

Firstly we need to allow insecure apps for our google accounts in order to get access fetch messages from Gmail using IMAP. We can do it [here](https://myaccount.google.com/lesssecureapps?pli=1).<br />
Then we need to receive OAuth 2.0 credentials to work with Google API. Follow [this link](https://gist.github.com/ivanvermeyen/cc7c59c185daad9d4e7cb8c661d7b89b) to see detailed instructions how to set up OAuth 2.0 for Google account.<br />
After we are done with accounts setup, all we need is to upload JSON configuration file (with OAuth credentials) from [Google API Console](https://console.developers.google.com/) to our PC.<br />
Now we can open PDF Uploader in browser and upload JSON configuration file from previous step and after OAuth procedure we can fill our gmail credentials in form and click on upload button, and then all valid pdf files will be uploaded to our Google Drive account (in root folder).
