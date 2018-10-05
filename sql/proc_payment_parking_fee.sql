CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_payment_parking_fee`(IN `i_year_month` VARCHAR(6), IN `i_flat_id` INT, IN `i_user_id` INT)
	LANGUAGE SQL
	NOT DETERMINISTIC
	CONTAINS SQL
	SQL SECURITY DEFINER
	COMMENT ''
BEGIN
	Declare v_cur_month varchar(8);
	Declare v_nex_month varchar(8);
	Declare v_pre_month varchar(8);
	Declare v_parking_hd_id int;
	Declare v_parkingfee_calculate_type int;
	
	SET v_cur_month = CONCAT(i_year_month, '01');
	SET v_nex_month = DATE_FORMAT(DATE_ADD(v_cur_month,INTERVAL 1 MONTH),'%Y%m%d');
	SET v_pre_month = DATE_FORMAT(DATE_ADD(v_cur_month,INTERVAL -1 MONTH),'%Y%m%d');
	
	update tf_payment_parking_hd a
	set 
		a.activation = 0,
		a.updated_at = CURRENT_TIMESTAMP(),
		a.updated_by = i_user_id
	where a.`year_month` = i_year_month and a.flat_id = i_flat_id;
	
	update tf_payment_parking_hd a, tf_payment_parking_dt b
	set 
		b.activation = 0,
		b.updated_at = CURRENT_TIMESTAMP(),
		b.updated_by = i_user_id
	where a.`year_month` = i_year_month and a.flat_id = i_flat_id and a.id = b.parking_hd_id;
	
			
	
	INSERT INTO tf_payment_parking_hd(
		flat_id
		,`year_month`
		,activation	
		,created_at	
		,updated_at	
		,created_by	
		,updated_by	)
	values
		(
		i_flat_id
		,i_year_month
		,1
		,CURRENT_TIMESTAMP()
		,CURRENT_TIMESTAMP()
		,i_user_id
		,i_user_id
	);
	
	SELECT LAST_INSERT_ID() into v_parking_hd_id;
	
	-- Thang truoc neu duoc gui xe giua chung
	INSERT INTO tf_payment_parking_dt(		
		    flat_id
			,parking_hd_id
			,`year_month`
			,parking_id
			,number_plate
			,name
			,label
			,maker
			,color
			,vehicle_type_id
			,vehicle_type
			,begin_contract_date
			,end_contract_date
			,driver
			,`comment`
			,from_date
			,to_date
			,month_days
			,activation	
		   ,created_at	
			,updated_at	
			,created_by	
			,updated_by
			,price
	)
	Select 
			 i_flat_id
			,v_parking_hd_id
			,i_year_month
			,a.id
			,number_plate
			,a.name
			,label
			,maker
			,color
			,vehicle_type_id
			,b.name
			,begin_contract_date
			,end_contract_date
			,driver
			,a.`comment`
			,begin_contract_date
			,v_cur_month
			,DATEDIFF(v_cur_month, v_pre_month)
			,1
			,CURRENT_TIMESTAMP()
			,CURRENT_TIMESTAMP()
			,i_user_id
			,i_user_id
			,b.price
	from tf_vehicle a left join tenement_parking_tariff b on a.vehicle_type_id = b.id
	where 
		a.flat_id = i_flat_id and 
		a.activation = 1 and
		a.begin_contract_date >= v_pre_month and
		a.begin_contract_date < v_cur_month;
	
	-- tron thang gui xe thang sau
	INSERT INTO tf_payment_parking_dt(		
		    flat_id
			,parking_hd_id
			,`year_month`
			,parking_id
			,number_plate
			,name
			,label
			,maker
			,color
			,vehicle_type_id
			,vehicle_type
			,begin_contract_date
			,end_contract_date
			,driver
			,`comment`
			,from_date
			,to_date
			,month_days
			,activation	
		   ,created_at	
			,updated_at	
			,created_by	
			,updated_by
			,price
	)
	Select 
			 i_flat_id
			,v_parking_hd_id
			,i_year_month
			,a.id
			,number_plate
			,a.name
			,label
			,maker
			,color
			,vehicle_type_id
			,b.name
			,begin_contract_date
			,end_contract_date
			,driver
			,a.`comment`
			,v_cur_month
			,ifnull(case when end_contract_date is not null and end_contract_date > v_nex_month then v_nex_month else end_contract_date end, v_nex_month)
			,DATEDIFF(v_nex_month,v_cur_month)
			,1
			,CURRENT_TIMESTAMP()
			,CURRENT_TIMESTAMP()
			,i_user_id
			,i_user_id
			,b.price
	from tf_vehicle a left join tenement_parking_tariff b on a.vehicle_type_id = b.id
	where 
		a.flat_id = i_flat_id and 
		a.activation = 1 and
		((a.end_contract_date <> '' and a.end_contract_date >= v_cur_month) or a.end_contract_date is null or a.end_contract_date = '') and
		a.begin_contract_date < v_cur_month;
		
	update tf_payment_parking_dt
	set parking_days = datediff(to_date, from_date)
	where parking_hd_id = v_parking_hd_id;
	
	Select a.parkingfee_calculate_type 
	from tenements a, tenement_flats b where a.id = b.tenement_id and a.id = i_flat_id
	into v_parkingfee_calculate_type;
	
	-- trên 16 ngày thì tính 1 tháng, dưới 16 ngày tính 1/2 tháng
	if v_parkingfee_calculate_type = 0 then
		update tf_payment_parking_dt a
		set a.total_money = (case when a.parking_days > 16 then a.price else a.price/2 end)
		where parking_hd_id = v_parking_hd_id;
	else
	-- tính theo ngày gửi
		update tf_payment_parking_dt a
		set a.total_money = a.price*a.parking_days/a.month_days
		where parking_hd_id = v_parking_hd_id;
	end if;


	Update tf_payment_parking_hd a,
	(
	select 
		a.parking_hd_id, 
		sum(ifnull(a.total_money,0)) total_money
	from tf_payment_parking_dt a
	where 
		a.activation = 1 and a.parking_hd_id = v_parking_hd_id
	group by a.parking_hd_id) as b
	set 
		a.total_money = b.total_money
	where a.id = b.parking_hd_id and b.parking_hd_id = v_parking_hd_id;
END