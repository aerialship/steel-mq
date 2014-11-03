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
token = null

progress
--------

deleted_at = null
available_at <= now
timeout_at != null

deleted
-------

deleted_at != null
timeout_at != null

expired
-------

deleted_at != null
timeout_at = null
