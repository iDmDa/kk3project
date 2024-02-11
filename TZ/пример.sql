DROP PROCEDURE IF EXISTS ConvertToDate;
DELIMITER //
CREATE PROCEDURE ConvertToDate(IN input_text VARCHAR(10))
BEGIN
    DECLARE output_date DATE;
    
    SET output_date = STR_TO_DATE(input_text, '%d.%m.%Y');
    
    SELECT output_date;
END //
DELIMITER ;

CALL ConvertToDate('01.01.2024')

SELECT * FROM mailbox 
WHERE STR_TO_DATE(datevh, '%d.%m.%Y') BETWEEN '2018-05-19' AND '2019-05-19' 
ORDER BY STR_TO_DATE(datevh, '%d.%m.%Y')