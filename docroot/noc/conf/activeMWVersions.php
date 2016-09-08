<?php

echo str_replace( ' ', ', ', exec( '/usr/bin/scap wikiversions-inuse' ) );
