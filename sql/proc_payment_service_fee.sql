CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_payment_service_fee`(IN `i_year_month` VARCHAR(6), IN `i_flat_id` INT, IN `i_user_id` INT)
	LANGUAGE SQL
	NOT DETERMINISTIC
	CONTAINS SQL
	SQL SECURITY DEFINER
	COMMENT ''
BEGIN
	Declare v_service_hd_id int;
	update tf_payment_service_hd a
	set 
		a.activation = 0,
		a.updated_at = CURRENT_TIMESTAMP(),
		a.updated_by = i_user_id
	where a.`year_month` = i_year_month and a.flat_id = i_flat_id;
	
	update tf_payment_service_hd a, tf_payment_service_dt b
	set 
		b.activation = 0,
		b.updated_at = CURRENT_TIMESTAMP(),
		b.updated_by = i_user_id
	where a.`year_month` = i_year_month and a.flat_id = i_flat_id and a.id = b.service_hd_id;
	
	INSERT INTO tf_payment_service_hd(
		flat_id,
		`year_month`)
	values
		(
		i_flat_id,
		i_year_month
	);
	
	SELECT LAST_INSERT_ID() into v_service_hd_id;
	
	INSERT INTO tf_payment_service_dt(		
		 flat_id	
		,service_hd_id	
		,`year_month`	
		,service_id	
		,service	
		,mount	
		,price	
		,unit	
		,vat	
		,vat_money	
		,other_fee01	
		,other_fee01_money	
		,other_fee02	
		,other_fee02_money	
		,total_money	
		,service_date_from	
		,service_date_to	
		,`comment`	
		,activation	
		,created_at	
		,updated_at	
		,created_by	
		,updated_by	
	)		
	SELECT 		
		 flat_id	
		,id	
		,`year_month`	
		,v_service_hd_id
		,name
		,mount	
		,price	
		,unit	
		,0	
		,0	
		,0	
		,0	
		,0	
		,0	
		,mount*price	
		,date_from	
		,date_to	
		,`comment`	
		,activation	
		,CURRENT_TIMESTAMP()	
		,CURRENT_TIMESTAMP()	
		,i_user_id	
		,i_user_id	
	FROM		
		tf_service_used	
	WHERE	flat_id = i_flat_id and	
		`year_month` = i_year_month and	
		activation = 1;
		
	-- update tf_payment_service_hd a, tf_payment_service_dt b
	-- set 
	-- 	a.total_money = sum(b.total_money), 
	-- 	a.activation = 1, 
	-- 	a.created_at = b.created_at, 
	-- 	a.updated_at = b.updated_at,
	-- 	a.created_by = b.created_by, 
	-- 	a.updated_by = b.updated_by
	-- where a.id = b.service_hd_id and a.id = v_service_hd_id;
	-- group by b.service_hd_id;
	
	Update tf_payment_service_hd a,
	(
	select 
		a.service_hd_id, 
		sum(ifnull(a.vat_money,0)) vat_money, 
		sum(ifnull(a.other_fee01_money,0)) other_fee01_money, 
		sum(ifnull(a.other_fee02_money,0)) other_fee02_money, 
		sum(ifnull(a.total_money,0)) total
	from tf_payment_service_dt a
	where 
		a.activation = 1 and a.service_hd_id = v_service_hd_id
	group by a.service_hd_id) as b
	set 
		a.vat_money = b.vat_money,
		a.other_fee01_money = b.other_fee01_money,
		a.other_fee02_money = b.other_fee02_money,
		a.total_money = b.total
	where a.id = b.service_hd_id and b.service_hd_id = v_service_hd_id;
END