SteelMQ API
===========

List Projects
-------------

```
GET /projects
```

### Response

``` json
[
    {
        "id": 123,
        "title": "Project name",
        "roles": ["owner", "default", "queue", "subscribe", "share"]
    }
]
```


Create Project
--------------

```
POST /projects
```

### Request

``` json
{
    "project": {
        "title": "Project name"
    }
}
```

### Response

``` json
{
    "id": 123,
    "success": true
}
```


Get project info
----------------

```
GET /projects/{Project ID}
```

### Response

``` json
{
    "id": 1359,
    "title": "Project Name",
    "roles": ["owner"]
}
```


List Message Queues
-------------------

```
GET /projects/{Project ID}/queues
```

### URL Parameters

* limit - optional, integer, defaults to 100, number of queues to return
* offset - optional, integer, defaults to 0


### Response

``` json
[
    {
        "id": 123,
        "title": "Queue name",
        "project_id": 456,
        "push_type": "unicast",
        "retries": 5,
        "retries_delay": 600,
        "error_queue": 678
    }
]
```



Create Message Queue
--------------------

```
POST /projects/{Project ID}/queues
```

### Body Parameters

* title - required, string, name of the queue to create
* push_type - optional, ```pull```, ```unicast```, or ```multicast```, defaults to ```pull```
* retries - optional, integer, defaults to 5, number of time to retry to deliver the message
* retries_delay - optional, integer, defaults to 600, number of seconds to wait before next retry
* error_queue - optional, integer, defaults to null, queue id to put messages to when message was not delivered after all retries
* timeout - optional, integer, defaults to 60, number of seconds taken message will timeout and put back on queue, can be overridden when creating message
* delay - optional, integer, defaults to 0, number of seconds until created messages gets available on the queue, can be overridden when creating message
* expires_in - optional, integer, defaults to 604800 (7 days), number of seconds to keep messages in the queue before they are deleted, can be overridden when creating message

### Request

``` json
{
    "queue": {
        "title": "New Queue Name",
        "push_type": "unicast",
        "retries": 5,
        "retries_delay": 600,
        "error_queue": 678,
        "timeout": 60,
        "delay": 0,
        "expires_in": 604800
    }
}
```

### Response

``` json
{
    "id": 123,
    "title": "Queue name",
    "project_id": 456,
    "push_type": "unicast",
    "retries": 5,
    "retries_delay": 600,
    "error_queue": 678
}
```

Requires role: ```queue```



Update Message Queue
--------------------

```
POST /projects/{Project ID}/queues/{Queue ID}
```

### Body Parameters

* title - required, string, name of the queue to create
* push_type - optional, ```pull```, ```unicast```, or ```multicast```, defaults to ```pull```
* retries - optional, integer, defaults to 5, number of time to retry to deliver the message
* retries_delay - optional, integer, defaults to 600, number of seconds to wait before next retry
* error_queue - optional, integer, defults to null, queue id to put messages to when message was not delivered after all retries

### Request

``` json
{
    "queue": {
        "title": "New Queue Name",
        "push_type": "unicast",
        "retries": 5,
        "retries_delay": 600,
        "error_queue": 678,
        "timeout": 60,
        "delay": 0,
        "expires_in": 604800
    }
}
```

### Response

``` json
{
    "id": 123,
    "title": "Queue name",
    "project_id": 456,
    "push_type": "unicast",
    "retries": 5,
    "retries_delay": 600,
    "error_queue": 678
}
```

Requires role: ```queue```




Delete Message Queue
--------------------

```
DELETE /projects/{Project ID}/queues/{Queue ID}
```

### Response

``` json
{
    "success": true
}
```

Requires role: ```queue```



Get Message Queue Info
----------------------

```
GET /projects/{Project ID}/queues/{Queue ID}
```

### Response

``` json
{
    "id": 123,
    "title": "Queue name",
    "project_id": 456,
    "push_type": "unicast",
    "retries": 5,
    "retries_delay": 600,
    "error_queue": 678,
    "timeout": 60,
    "delay": 0,
    "expires_in": 604800
    "size": 12
}
```


Clear Message Queue
-------------------

```
POST /projects/{Project ID}/queues/{Queue ID}/clear
```

### Response

``` json
{
    "success": true
}
```

Requires role: ```queue```



List Message Queue Subscribers
------------------------------

```
GET /projects/{Project ID}/queues/{Queue ID}/subscribers
```

### URL Parameters

* limit - optional, integer, defaults to 100, number of subscribers to return
* offset - optional, integer, defaults to 0

### Response

``` json
[
    {
        "id": 123,
        "url": "http://some.subscriber.com/steel_mq_hook",
        "headers": {
            "Content-Type": [
                "application/json"
            ],
            "X-Custom": [
                "foo",
                "bar"
            ]
        }
    }
]
```


Add Subscriber to Message Queue
-------------------------------

```
POST /projects/{Project ID}/queues/{Queue ID}/subscribers
```

### Request

``` json
{
    "subscriber": {
        "url": "http://some.subscriber.com/steel_mq_hook",
        "headers": {
            "Content-Type": [
                "application/json"
            ],
            "X-Custom": [
                "foo",
                "bar"
            ]
        }
    }
}
```

### Response

``` json
{
    "id": 123,
    "success": true
}
```

Requires role: ```subscribe```


Delete Subscriber from Message Queue
------------------------------------

```
DELETE /projects/{Project ID}/queues/{Queue ID}/subscribers/{Subscriber ID}
```

### Response

``` json
{
    "success": true
}
```


Requires role: ```subscribe```




Add Messages to Queue
---------------------

```
POST /projects/{Project ID}/queues/{Queue ID}/messages
```

### Request

``` json
{
    "messages": [
        {
            "body": "Message string body",
            "delay": 0,
            "retries": 3
        }
    ]
}
```


Webhook
-------

Creates a message with body equal to posted content. 

```
POST /projects/{Project ID}/queues/{Queue ID}/messages/webhook
```


Get Message from Queue
----------------------

```
GET /projects/{Project ID}/queues/{Queue ID}/messages
```

### URL Parameters

* limit - optional, integer, defaults to 1, maximum is 100. Note that you might not receive all requested messages, but only those available at the moment of the request
* timeout - optional, integer, defaults to message queue timeout which defaults to 60, number of seconds after which message will be placed back on the queue, unless deleted or timeout expanded
* delete - optional, boolean, defaults to false, if true message will be deleted immediately on this request. Be careful and use this only if losing a message is ok

### Response

``` json
{
    "messages": [
        {
            "id": 123,
            "body": "Body message string",
            "timeout": 60,
            "retries_remaining": 3
        }
    ]
}
```


Get Message by ID
-----------------

```
GET /projects/{Project ID}/queues/{Queue ID}/messages/{Message ID}
```

### Response

``` json
{
    "id": 123,
    "body": "Message Body String",
    "timeout": 60,
    "created_at": "2014-01-01 12:25:36Z",
    "completed_at": "2014-01-01 14:18:54Z"
}
```


Delete Message from Queue
-------------------------

```
DELETE /projects/{Project ID}/queues/{Queue ID}/messages/{Message ID}
```

### Response

``` json
{
    "success": true
}
```


Release Message back to Queue
-----------------------------

```
POST /projects/{Project ID}/queues/{Queue ID}/messages/{Message ID}/release
```

### Body Parameters

* delay - optional, integer, defaults to 0, number of seconds until message becomes available on the queue

### Request

``` json
{
    "delay": 60
}
```

### Response

``` json
{
    "success": true
}
```

