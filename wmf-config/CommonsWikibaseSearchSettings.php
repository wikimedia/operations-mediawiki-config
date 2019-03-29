<?php

// T218954
// Disable dispatching query builder, which provides specialized
// entity full text search, until it meets the commons use case.
$wgWBCSEnableDispatchingQueryBuilder = false;

