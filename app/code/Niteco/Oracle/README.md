# Niteco Oracle Extension

Niteco Oracle help us send order to Oracle 

## Requirement
1. Magento v2.3.0
1. Enable Redis cache
2. Enable send email default
## Install

1. Create folder in magento2 project: /app/code/Niteco/Oracle
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
5. Go to: General Contact and Change email -> save

## Show queues
Use command
```bash
    php bin/magento niteco:oracle:queues
```

## Check data

*Go to mysql admin, use database*

1. Check status order

User query
```bash
select * from niteco_oracle_schedule;
```

2. Check CronJob message, status, time

```bash
select * from cron_schedule where job_code like '%NitecoOracleSendOrders%';

select * from cron_schedule where job_code like '%NitecoOracleSendEmail%';
```

## Check log
File log save with name: 
1. /project-name/var/log/niteco_oracle_sent_email.log
2. /project-name/var/log/niteco_oracle_sent_oracle.log

