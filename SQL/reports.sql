-- ilość przeprowadzonych rozmów
SELECT * FROM no_of_succeeded;

-- czasy
SELECT * FROM
  v_avg_timings;
  
  
  -- ile rekordów zostało do przedzwonienia
  SELECT a.operator, a.left, b.all FROM
(SELECT operator, COUNT(1) AS `left` FROM
  v_left_contacts GROUP BY operator) a 
  INNER JOIN (SELECT operator, COUNT(1) AS `all` FROM v_contacts GROUP BY operator) b  ON a.operator=b.operator;
  
  -- tempo
  SELECT calls.operator, calls.date,  calls.succeeded, worktime.worktime /60 AS `worktime`, calls.succeeded / (worktime.worktime/60) AS `tempo` FROM  
    (SELECT operator, DATE(contact_date) AS `date`, TIMESTAMPDIFF(MINUTE, min(contact_date) , max(contact_date)) `worktime` FROM v_contacts 
      GROUP BY operator, DATE(contact_date) ) AS worktime
  INNER JOIN 
      (SELECT operator, DATE(contact_date) AS `date`, count(1) AS succeeded
       FROM v_contacts 
       WHERE status = 'Przeprowadzona'
      Group By operator,DATE(contact_date)) AS calls
  ON worktime.operator = calls.operator AND worktime.date = calls.date;
  
  
  
  --tempo matrix
SELECT cols.operator, cols.date,  calls.succeeded, worktime.worktime /60 AS `worktime`, calls.succeeded / (worktime.worktime/60) AS `tempo` FROM
(SELECT date, operator FROM 
	(SELECT DATE(contact_date) AS `date`
       	FROM v_contacts 
       	WHERE status = 'Przeprowadzona'
      	Group By DATE(contact_date)) AS dates,
	(SELECT operator FROM v_contacts WHERE operator IS NOT NULL GROUP BY operator) AS operators) AS cols
        LEFT JOIN
        (SELECT operator, DATE(contact_date) AS `date`, TIMESTAMPDIFF(MINUTE, min(contact_date) , max(contact_date)) `worktime` FROM v_contacts 
      GROUP BY operator, DATE(contact_date) ) AS worktime ON cols.date = worktime.date AND cols.operator = worktime.operator
      LEFT JOIN (SELECT operator, DATE(contact_date) AS `date`, count(1) AS succeeded
       FROM v_contacts 
       WHERE status = 'Przeprowadzona'
      Group By operator,DATE(contact_date)) AS calls
  ON worktime.operator = calls.operator AND worktime.date = calls.date;