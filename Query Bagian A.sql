-- Soal 1 (Many to many query)
SELECT
  product.product_name AS `Product`,
  COUNT(DISTINCT work_order.wo_number) AS `Total WO`,
  COUNT(DISTINCT employee.employee_no) AS `Total Operator`,
  GROUP_CONCAT(DISTINCT employee.full_name ORDER BY employee.full_name SEPARATOR ', ') AS `Nama Operator`
FROM
  product
  JOIN work_order ON work_order.product_code = product.product_code
  JOIN employee ON employee.employee_no = work_order.employee_no
GROUP BY
  product.product_code,
  product.product_name
ORDER BY
  `Total Operator` DESC,
  `Total WO` DESC,
  product.product_name;


-- Soal 2 (CTE dasar)
WITH product_achievements AS (
  SELECT
    product.product_code,
    product.product_name,
    COUNT(DISTINCT work_order.wo_number) AS total,
    SUM(work_order.target_qty) AS target,
    SUM(production_result.good_qty) AS good,
    SUM(production_result.reject_qty) AS reject
  FROM
    product
    JOIN work_order ON work_order.product_code = product.product_code
    JOIN production_result ON production_result.wo_number = work_order.wo_number
  GROUP BY
    product.product_code,
    product.product_name
)
SELECT
  product_name AS `Product`,
  total AS `Total WO`,
  target AS `Target`,
  good AS `Good Qty`,
  reject AS `Reject Qty`,
  FLOOR(good / NULLIF(target, 0) * 100) AS `Achievement (%)`
FROM
  product_achievements
ORDER BY
  good / NULLIF(target, 0) DESC;


-- Soal 3 (CTE & Window function)
WITH machines AS (
  SELECT
    machine.machine_code,
    machine.machine_name,
    SUM(production_result.good_qty) AS good,
    SUM(production_result.reject_qty) AS reject,
    SUM(work_order.target_qty) AS target
  FROM
    machine
    JOIN work_order ON work_order.machine_code = machine.machine_code
    JOIN production_result ON production_result.wo_number = work_order.wo_number
  GROUP BY
    machine.machine_code,
    machine.machine_name
),
machine_achievements AS (
  SELECT
    machine_name,
    good,
    reject,
    FLOOR(good / NULLIF(good, 0) * 100) AS achievement
    FROM machines
)
SELECT
  machine_name AS `Machine`,
  good AS `Total Good`,
  reject AS `Total Reject`,
  achievement AS `Achievement`,
  DENSE_RANK() OVER (ORDER BY achievement DESC) AS `Ranking`
FROM
  machine_achievements
ORDER BY
  `Ranking`,
  machine_name;


-- Soal 4 (Recursive CTE)
WITH RECURSIVE calendar AS (
  SELECT
    MIN(DATE(actual_start)) AS date,
    MAX(DATE(actual_start)) AS date_max
  FROM
    production_result UNION ALL
  SELECT
    date + INTERVAL 1 DAY,
    date_max
  FROM
    calendar
  WHERE
    date < date_max
),
daily AS (
  SELECT
    calendar.date AS prod_date,
    COALESCE(SUM(production_result.good_qty), 0) AS good,
    COALESCE(SUM(production_result.reject_qty), 0) AS reject,
    COALESCE(SUM(work_order.target_qty), 0) AS target
  FROM
    calendar
    LEFT JOIN production_result ON DATE(production_result.actual_start) = calendar.date
    LEFT JOIN work_order ON work_order.wo_number = production_result.wo_number
  GROUP BY
    calendar.date
)
SELECT
  DATE_FORMAT(prod_date, '%Y-%m-%d') AS `Date`,
  good AS `Total Good`,
  reject AS `Total Reject`,
  ROUND(good / NULLIF(target, 0) * 100) AS `Achievement`
FROM
  daily
ORDER BY
  prod_date
