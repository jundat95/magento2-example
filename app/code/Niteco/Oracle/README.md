# Niteco Oracle Extension

Niteco Oracle help us send order to Oracle 

## Install

1. Create folder in magento2 project: /app/Niteco/Oracle
2. `git clone https://github.com/tinh-ngo-niteco/niteco-oracle.git`
3. Copy code to folder: /app/Niteco/Oracle
4. Run command in project
```bash
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:clean
php bin/magento cache:flush
```

## Use

1. Go to admin page: Stores -> Configurations -> Sales -> Niteco Oracle
2. Enable: Send Order To Oracle, Send EMail When Error
3. Input email to: Notify e-mails when error occurs

4. Change email default, go to: Stores -> General -> Store Email Addresse
5. Change email -> save


## Check log
File log save with name: 
  1. /project-name/var/log/niteco_oracle_sent_email.log
  2. /project-name/var/log/niteco_oracle_sent_oracle.log

