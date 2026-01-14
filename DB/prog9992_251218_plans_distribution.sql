-- Agregar campo para distribución de intereses en planes
-- Fecha: 2025-12-18
-- Descripción: Permite configurar distribución de intereses por segmentos temporales

ALTER TABLE `plans` ADD `interest_distribution` TEXT NULL AFTER `status`;

-- Ejemplo de estructura JSON para interest_distribution:
-- {
--   "enabled": true,
--   "segments": [
--     {
--       "segment": 1,
--       "months": 4,
--       "percentage": 2,
--       "description": "Período inicial"
--     },
--     {
--       "segment": 2,
--       "months": 4,
--       "percentage": 6,
--       "description": "Período de crecimiento"
--     },
--     {
--       "segment": 3,
--       "months": 4,
--       "percentage": 7,
--       "description": "Período de madurez"
--     }
--   ]
-- }
