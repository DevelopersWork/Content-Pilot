CREATE OR REPLACE VIEW 
    %table_prefix%jobs_services_secrets_map 
AS
    SELECT 
        DISTINCT jobs.id as job_id, jobs.service_id, trigger_id, secrets.id secret_id
    FROM  
    (
        SELECT 
            id, service_id, trigger_id 
        FROM 
            %table_prefix%jobs 
        WHERE disabled = 0 AND deleted = 0
    ) AS jobs
    JOIN 
    (
        SELECT 
            service_id, id 
        FROM 
            %table_prefix%secrets 
        WHERE disabled = 0
    ) AS secrets
    ON secrets.service_id = jobs.service_id;