#!/bin/bash
/usr/bin/php think queue:listen --queue SendWxTplMsg > /dev/null 2>&1 &
/usr/bin/php think queue:listen --queue TransferMedia > /dev/null 2>&1 &
/usr/bin/php think queue:listen --queue Base > /dev/null 2>&1 &