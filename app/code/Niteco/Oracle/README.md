# Niteco Oracle Extension

Niteco Oracle help us send orders to Oracle 

## Requirement
1. Magento v2.3.0
1. Enable Redis cache
2. Enable send email default
## Install

1. Create folder in magento2 project: /app/code/Niteco/Oracle
2. Clone github: `git clone https://github.com/tinh-ngo-niteco/niteco-oracle.git`
3. Copy code to folder: /app/Niteco/Oracle
4. Run command in project
```bash
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:clean
php bin/magento cache:flush
```

## Use

1. Go to admin page: *Stores -> Configurations -> Sales -> Niteco Oracle*
2. Enable: Send order to Oracle, Send email when error
3. Input email to: Notify e-mails when error occurs

4. Change email default, go to: *Stores -> Configurations -> General -> Store Email Address*
5. Go to: General Contact and change email
6. Save

# Config crontab

1. Open file: /project-name/app/code/Niteco/Oracle/etc/crontab.xml
2. Change time schedule
```bash
<job name="NitecoOracleSendOrders" instance="Niteco\Oracle\Cron\SendOrders" method="execute">
    <schedule>* * * * *</schedule>
</job>
```

# Change config cron groups

1. Go to admin page: *Stores -> Configurations -> Advanced -> System*
2. Go to niteco groups
3. Change config
4. Save 

## Command line
### Use command check redis work, and show queues
```bash
    php bin/magento niteco:oracle queues [queues2] [queues3]
```
### Send orders
```bash
    php bin/magento niteco:oracle sendorders [sendorders2] [sendorders3]
```
### Send email
```bash
    php bin/magento niteco:oracle sendmails
```

## Check data

*Go to mysql admin, use database*

1. Check status order in schedule

User mysql query
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

## Check order send to Oracle success

1. Check file log
*/project-name/var/log/niteco_oracle_sent_oracle.log*

2. Go to Sales -> Orders and 
*Check text: Transferred to Oracle*





