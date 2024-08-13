<?php

declare(strict_types=1);

return [
    /**
     * if defined, the Email model will use this database connection.
     * This connection name must be defined in database.connections config file
     */
    'email-db-connection' => null,

    /**
     * Save the HTML Body of all sent messages
     */
    'log-body-html' => true,

    /**
     * Save the TXT Body of all sent messages
     */
    'log-body-txt' => true,
];
