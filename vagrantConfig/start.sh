#!/bin/sh
# ---------------------------------------------------------------------------
# Copyright 2017, BuzzingPixel, LLC
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the Apache License 2.0.
# http://www.apache.org/licenses/LICENSE-2.0
# ---------------------------------------------------------------------------

# Make sure NGINX restarts after NFS is mounted so it can pick up the NGINX conf
sudo service nginx restart;
