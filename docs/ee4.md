# Notes on ExpressionEngine 4

Almost all of Executive is compatible with EE 4. Almost. Unfortunately, because of the changes to the way channels and fields work, some big changes had to happen to the ChannelDesigner class — which is a big part of making migrations super easy. These changes are backwards incompatible. Therefore, the major version of Executive has been incremented to 2.x.x for the update for EE 4 and Executive 2 required EE 4.

This also means that you will need to make sure all migrations written for EE 3 run **before** updating to EE 4 — or alternately you can re-write those migrations for EE 4 (which actually may not be that hard).
