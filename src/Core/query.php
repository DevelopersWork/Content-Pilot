<?php

$query = "
SELECT 
    ref.*,
    jobs.name AS job_name,
    secrets.value AS secret,
    services.name AS service_name,
    triggers.name AS trigger_name, triggers.type, triggers.seconds, triggers.minutes, triggers.hours, triggers.days
FROM 
    WordpressPlugins_ContentPilot_jobs_services_secrets_map AS ref
    JOIN 
        WordpressPlugins_ContentPilot_jobs AS jobs ON jobs.id = ref.job_id
    JOIN 
        WordpressPlugins_ContentPilot_secrets AS secrets ON secrets.id = ref.secret_id
    JOIN 
        WordpressPlugins_ContentPilot_services AS services ON services.id = ref.service_id
    JOIN 
        WordpressPlugins_ContentPilot_triggers AS triggers ON triggers.id = ref.trigger_id
";

$_result = $wpdb->get_row( $query );
// use Dev\WpContentAutopilot\Core\YouTube;
// $yt = new YouTube($this -> store);
// $yt -> makePost();