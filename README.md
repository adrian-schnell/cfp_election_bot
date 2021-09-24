## DeFiChain CFP Voting - Telegram Bot

### Description

This app sends current results of a running CFP voting. The current proposals can be found at
[GitHub](https://github.com/DeFiCh/dfips/issues/).

### Usage

- Add the bot to your telegram account: [https://t.me/DFI_cfp_election_bot](https://t.me/DFI_cfp_election_bot)
- setup the bot in the wizard
  - you can choose the notification intervall
  - you can choose, if you want to get informed on certain CFP or all results

### Commands

- **/cfp GITHUB_ID**: get the current result of a certain CFP on demand
- **/cfp_all**: get all current results on demand
- **/settings**: change the notification intervall (or disable notifications)
- **/select_cfp**: change the sent CFP in the notifications

### technical details

This app is build on the Laravel Framework. To use it by yourself take a look at the
[official docu](https://laravel.com/docs/8.x/installation).
