<?php
namespace Ytake\VoltDB;

/**
 * Class SystemProcedure
 *
 * VoltDB provides system procedures that perform system-wide administrative functions.
 *
 * @package Ytake\LaravelVoltDB
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @see https://voltdb.com/docs/UsingVoltDB/AppSysProc.php
 */
final class SystemProcedure
{

    // — Executes an SQL statement specified at runtime.
    const AD_HOC = "@AdHoc";

    // — Returns the execution plan for the specified SQL query.
    const EXPLAIN = "@Explain";

    // — Returns the execution plans for all SQL queries in the specified stored procedure.
    const EXPLAIN_PROC = "@ExplainProc";

    // — Returns a list of partition values, one for every partition in the database.
    const GET_PARTITION_KEYS = "@GetPartitionKeys";

    // — Initiates admin mode on the cluster.
    const PAUSE = "@Pause";

    // — Promotes a replica database to normal operation.
    const PROMOTE = "@Promote";

    // — Waits for all queued export data to be written to the connector.
    const QUIESCE = "@Quiesce";

    //  — Returns a paused database to normal operating mode.
    const RESUME = "@Resume";

    // — Shuts down the database.
    const SHUTDOWN = "@Shutdown";

    // — Deletes one or more native snapshots.
    const SNAPSHOT_DELETE = "@SnapshotDelete";

    // — Restores a database from disk using a native format snapshot.
    const SNAPSHOT_RESTORE = "@SnapshotRestore";

    // — Saves the current database contents to disk.
    const SNAPSHOT_SAVE = "@SnapshotSave";

    // — Lists information about existing native snapshots in a given directory path.
    const SNAPSHOT_SCAN = "@SnapshotScan";

    // — Lists information about the most recent snapshots created from the current database.
    const SNAPSHOT_STATUS = "@SnapshotStatus";

    // — Returns statistics about the usage of the VoltDB database.
    const STATISTICS = "@Statistics";

    // — Stops a VoltDB server process, removing the node from the cluster.
    const STOP_NODE = "@StopNode";

    // — Returns metadata about the database schema.
    const SYSTEM_CATALOG = "@SystemCatalog";

    // — Returns configuration information about VoltDB and the individual nodes of the database cluster.
    const SYSTEM_INFO = "@SystemInformation";

    // — Reconfigures the database by replacing the application catalog currently in use.
    const UPDATE_APP_CATALOG = "@UpdateApplicationCatalog";

    // — Changes the logging configuration for a running database.
    const UPDATE_LOG = "@UpdateLogging";
} 