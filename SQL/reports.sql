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
  