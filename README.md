NewRelic Insights for Woocommerce
==============

Adds NewRelic Insights integration to WooCommerce by tracking key metrics
--------------

## Requirements
Make sure you have a New Relic account before starting. To see all the features you will need a New Relic Pro subscription (or equivalent).
Tested with Woocommerce 2.1+ and Wordpress 3.8.3

## Version History

v.0.0.5
Added viewed_product method

v.0.0.4
Added payment_method_title to nri_completed_purchase

v.0.0.3
Fixed typo in naming cart_total

v.0.0.2
Add activate , deactivate functions

v.0.0.1
Initial

## Example NRQL Queries

Once you've loaded some of your data, what kind of things can you find out?

### Top 10 Countries in last 8 hours

```sql
SELECT count(*) FROM PageView WHERE appName='<your domain>' FACET countryCode LIMIT 10 SINCE 8 hours ago
```

### Top 10 Countries in last 8 hours

```sql
SELECT count(*) FROM PageView WHERE appName='<your domain>' FACET countryCode LIMIT 10 SINCE 8 hours ago
```

### Viewed cart today

```sql
SELECT count(*) From Transaction WHERE viewed_cart > 0 since 1 day ago
```

### Started checkout process today

```sql
SELECT count(*) FROM Transaction WHERE started_checkout > 0 since 1 day ago
```

### Completed purchase today

```sql
SELECT count(*) FROM Transaction WHERE completed_purchase > 0 since 1 day ago
```

### Sales per hour (last 12 hours)

```sql
SELECT count(*) FROM Transaction WHERE completed_purchase > 0 TIMESERIES 30 minutes SINCE  12 hours ago
```

### Sales perday this week

```sql
SELECT count(*) FROM Transaction WHERE completed_purchase > 0 TIMESERIES 1 day SINCE 7 days ago
```
