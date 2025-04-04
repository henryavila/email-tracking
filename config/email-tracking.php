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


    /**
     * When the message_id of mailgun is not found in the database, log it as a warning
     */
    'log-email-not-found' => true,

    /**
     * For every email event, save it in the database in EmailEvent model
     * This is useful to see the events of every email sent in the system
     */
    'save-email-event-in-database' => true,

    /**
     * The table name for the EmailEventLog model
     */
    'email-event-logs-table' => 'email_event_logs',
];
