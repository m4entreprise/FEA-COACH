-- Vérifier si la colonne has_completed_onboarding existe
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    COLUMN_DEFAULT
FROM 
    INFORMATION_SCHEMA.COLUMNS 
WHERE 
    TABLE_NAME = 'users' 
    AND COLUMN_NAME = 'has_completed_onboarding';

-- Si la colonne n'existe pas, l'ajouter :
-- ALTER TABLE users ADD COLUMN has_completed_onboarding BOOLEAN DEFAULT FALSE AFTER setup_completed_at;

-- Pour réinitialiser le tutoriel pour tous les utilisateurs (test) :
-- UPDATE users SET has_completed_onboarding = FALSE;

-- Pour réinitialiser le tutoriel pour un utilisateur spécifique :
-- UPDATE users SET has_completed_onboarding = FALSE WHERE email = 'votre@email.com';
