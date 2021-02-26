---eonx_docs---
title: 'Stores'
weight: 1005
---eonx_docs---

# Stores

The EasyWebhook package allows you to store webhooks and webhook results within the persisting layer of your choice.
Different stores can be used for webhooks and webhook results.

You can implement your own stores, but the package comes with three store options out of the box: null store, array
store and Doctrine DBAL store.

To set the **webhook store**, set the `EonX\EasyWebhook\Interfaces\Stores\StoreInterface` service to be one of:

- `EonX\EasyWebhook\Stores\NullStore`: Webhooks are not stored. This is the default store option for webhooks.
- `EonX\EasyWebhook\Stores\ArrayStore`: Webhooks are stored in an array. Note that the array store will not persist
  beyond the life of your application.
- `EonX\EasyWebhook\Stores\DoctrineDbalStore`: Webhooks are stored in a database accessed through Doctrine DBAL. Provide
  a `Doctrine\DBAL\Connection` connection and an optional table name (the default table name is `easy_webhooks`).
- Your own webhook store implementation.

To set the **webhook results store**, set the `EonX\EasyWebhook\Interfaces\Stores\ResultStoreInterface` service to be
one of:

- `EonX\EasyWebhook\Stores\NullResultStore`: Webhook results are not stored. This is the default store option for
  webhook results.
- `EonX\EasyWebhook\Stores\ArrayResultStore`: Webhook results are stored in an array. Note that the array store will not
  persist beyond the life of your application.
- `EonX\EasyWebhook\Stores\DoctrineDbalResultStore`: Webhook results are stored in a database accessed through Doctrine
  DBAL. Provide a `Doctrine\DBAL\Connection` connection and an optional table name (the default table name is
  `easy_webhook_results`).
- Your own webhook results store implementation.
