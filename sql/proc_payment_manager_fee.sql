BEGIN
	Declare v_cur_month varchar(8);
	Declare v_nex_month varchar(8);
	Declare v_pre_month varchar(8);
	
	Declare v_area decimal;
	Declare v_manager_fee decimal;
	Declare v_managerfee_calculate_type decimal;
	Declare v_manager_day int;
	Declare v_day_in_month int;
	Declare v_receive_date varchar(8);
	
	SET v_cur_month = CONCAT(i_year_month, '01');
	SET v_nex_month = DATE_FORMAT(DATE_ADD(v_cur_month,INTERVAL 1 MONTH),'%Y%m%d');
	SET v_pre_month = DATE_FORMAT(DATE_ADD(v_cur_month,INTERVAL -1 MONTH),'%Y%m%d');
	
	-- SELECT v_pre_month, v_cur_month, v_nex_month;
	
	SELECT a.manager_fee, a.managerfee_calculate_type, b.area, b.receive_date 
	FROM tenements a, tenement_flats b
	WHERE 
		b.id = i_flat_id AND
		a.id = b.tenement_id
	INTO v_manager_fee, v_managerfee_calculate_type, v_area, v_receive_date;
		
	-- SELECT v_manager_fee, v_managerfee_calculate_type, v_area, v_receive_date;
	
	if v_receive_date < v_cur_month then
		set v_receive_date = v_cur_month;
	end if;
	-- Thu phí trước
	if v_managerfee_calculate_type = 1 then
		SET v_manager_day = DATEDIFF(v_nex_month, v_receive_date);
		SET v_day_in_month = DATEDIFF(v_nex_month, v_cur_month);
		-- SELECT v_day_in_month, v_manager_day;
		
		if (v_manager_day >= v_day_in_month) then
			SET v_manager_fee = v_manager_fee*v_area;
		else
			SET v_manager_fee = (v_manager_fee*v_area)*v_manager_day/v_day_in_month;
		end if;
	else
		-- Thu phí sau
		-- select 123;
		SET v_manager_day = DATEDIFF(v_cur_month, v_receive_date);
		SET v_day_in_month = DATEDIFF(v_cur_month, v_pre_month);
		SELECT v_day_in_month, v_manager_day;
		
		if (v_manager_day >= v_day_in_month) then
			SET v_manager_fee = v_manager_fee*v_area;
		else
			SET v_manager_fee = (v_manager_fee*v_area)*v_manager_day/v_day_in_month;
		end if;
	end if;
	
	if v_manager_day < 0 then
		set v_manager_fee = 0;
	end if;
	
	update tf_payment_manager a
	set 
		a.activation = 0,
		a.updated_at = CURRENT_TIMESTAMP(), 
		a.updated_by = i_user_id
	where a.flat_id = i_flat_id and a.`year_month` = i_year_month;
	
	INSERT INTO tf_payment_manager (	
		 flat_id
		,`year_month`
		
		,flat_code
		,name
		,email
		,owner
		,address
		,block_name
		,floor_num
		,floor_name
		,flat_num
		,phone
		
		,`area`
		,manager_price
		,persons
		,`comment`
		,water_type_id
		,display_code
		,receive_date
		,sum_pre_paid
		,elec_type_id
		,investor
		,is_stay
		,gas_type_id
		,bill_no
		
		,day_in_month
		,manager_day
		,vat
		,other_fee01
		,other_fee02
		,vat_money
		,other_fee01_money
		,other_fee02_money
		,total_money
		
		,activation
		,created_at
		,updated_at
		,created_by
		,updated_by
	)	
	SELECT 
		 i_flat_id
		,i_year_month
		,flat_code
		,name
		,email
		,owner
		,address
		,block_name
		,floor_num
		,floor_name
		,flat_num
		,phone

		,`area`
		,manager_price
		,persons
		,`comment`
		,water_type_id
		,display_code
		,receive_date
		,sum_pre_paid
		,elec_type_id
		,investor
		,is_stay
		,gas_type_id
		,bill_no
		
		,v_day_in_month
		,v_manager_day
		,0
		,0
		,0
		,0
		,0
		,0
		,v_manager_fee
		
		,1
		,CURRENT_TIMESTAMP()
		,CURRENT_TIMESTAMP()
		,i_user_id
		,i_user_id
		
	FROM tenement_flats a
	WHERE a.id = i_flat_id;
	
	
	-- SELECT v_manager_fee;
END