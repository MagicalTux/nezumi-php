<?
/* SERVER LOG
 * Allow easy logging of events
 */

// Log levels
define("SLOG_DEBUG",0);
define("SLOG_INFO",1);
define("SLOG_NOTICE",2);
define("SLOG_WARNING",3);
define("SLOG_WARN",SLOG_WARNING);
define("SLOG_ALERT",4);
define("SLOG_FATAL",5);

function serverlog($level,$message) {
  