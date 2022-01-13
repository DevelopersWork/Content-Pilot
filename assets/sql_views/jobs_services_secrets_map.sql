CREATE OR REPLACE VIEW 
    %table_prefix%_jobs_services_secrets_map 
AS

SELECT
    jobs.id AS job_id, jobs.meta_id, jobs.trigger_id, meta.service_id, secrets.id AS secret_id
FROM 
    (SELECT id, meta_id, trigger_id FROM %table_prefix%_jobs WHERE disabled = 0 AND deleted = 0) AS jobs
JOIN
    (SELECT id, service_id, secret_id, key_required FROM %table_prefix%_meta WHERE disabled = 0 AND deleted = 0) AS meta
    ON jobs.meta_id = meta.id
JOIN
    (SELECT id FROM %table_prefix%_services WHERE disabled = 0) AS services 
    ON meta.service_id = services.id
JOIN
    (SELECT id FROM %table_prefix%_triggers WHERE disabled = 0 AND deleted = 0) AS triggers 
    ON jobs.trigger_id = triggers.id
LEFT JOIN
    (SELECT id, service_id FROM %table_prefix%_secrets WHERE disabled = 0 AND deleted = 0) AS secrets
    ON 
        (meta.secret_id = secrets.id AND meta.key_required = 1) OR 
        (services.id = secrets.service_id AND meta.key_required = 1 AND meta.secret_id IS NULL AND secrets.service_id = meta.service_id);
