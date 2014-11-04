Message Status
==============

not available
-------------

deleted_at = null
available_at > now


available
---------

deleted_at = null
available_at <= now


progress
--------

deleted_at = null
available_at <= now
timeout_at > now


timed-out
---------

deleted_at = null
available_at <= now
timeout_at <= now


deleted
-------

deleted_at != null
timeout_at != null

expired
-------

created_at + queue.expires_in < now
