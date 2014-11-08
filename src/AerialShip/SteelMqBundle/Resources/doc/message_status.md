Message Status
==============

not available
-------------

available_at > now


available
---------

available_at <= now
token = null

progress
--------

token != null
timeout_at > now


timed-out
---------

timeout_at <= now


expired
-------

created_at + queue.expires_in < now
