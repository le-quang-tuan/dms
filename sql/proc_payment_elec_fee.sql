BEGIN
	Declare v_used decimal;
	Declare v_used_temp decimal;
	Declare v_elec_type_id int;
	Declare v_elec_type varchar(125);
	Declare v_name varchar(125);
	Declare v_index_from decimal;
	Declare v_price decimal;
	Declare v_other_fee01 decimal; 
	Declare v_other_fee02 decimal;
	Declare v_vat decimal;
	
	Declare v_other_fee01_money decimal; 
	Declare v_other_fee02_money decimal;
	Declare v_vat_money decimal;
	Declare v_total_money decimal;
	
	Declare v_elec_hd_id int;
	Declare v_row_no int;
	
	DECLARE done INT DEFAULT FALSE;
	
	--  lấy danh sách các biểu phí và sắp xếp theo chế độ chỉ số thấp dần
	DECLARE elec_tariff_cur CURSOR FOR		
		SELECT a.elec_type_id, c.elec_type, b.name, b.index_from, b.price, b.other_fee01, b.other_fee02, b.vat
		FROM tenement_flats a, tenement_elec_tariff b, tenement_elec_types c
		WHERE 
			a.elec_type_id = b.elec_type_id AND
			b.activation = 1 AND
			a.id = i_flat_id AND
			b.elec_type_id = c.id
		ORDER BY b.index_from desc;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	-- update xóa hết các dữ liệu đã có bằng cách set flag = 1
-- update tf_payment_elec_hd a
-- set
-- a.activation = 0,
-- a.updated_at = CURRENT_TIMESTAMP(),
-- a.updated_by = i_user_id
-- 	where a.flat_id = i_flat_id and a.`year_month` = i_year_month;
	
	-- update tf_payment_elec_dt a
	-- update tf_payment_elec_dt a, tf_payment_elec_hd b
-- set
-- a.activation = b.activation,
-- a.updated_at = b.updated_at,
-- a.updated_by = b.updated_by
-- 	where b.flat_id = i_flat_id and b.`year_month` = i_year_month and a.elec_hd_id = b.id;

	
	-- lấy số lượng tổng dùng
	SELECT a.new_index - a.old_index
	FROM tf_elec_used a
	WHERE 
		a.`year_month` = i_year_month and
		a.activation = 1 and
		a.flat_id = i_flat_id
	INTO v_used;

	-- lấy thông tin chỉ số tháng
	-- SELECT i_flat_id, b.id, b.elec_type, c.id, c.date_from, c.date_to, c.old_index, c.new_index, c.new_index - c.old_index, c.`comment`, CURRENT_TIMESTAMP(), i_user_id
	-- FROM tenement_flats a, tenement_elec_types b, tf_elec_used c
	-- WHERE 
		-- a.tenement_id = b.tenement_id and
		-- a.id = c.flat_id and
		-- a.id = i_flat_id and
		-- c.`year_month` = i_year_month and
		-- a.activation = 1 and
		-- b.activation = 1 and
		-- c.activation = 1 and
		-- a.elec_type_id = b.id;
	-- INTO v_used;
	-- INSERT INTO tf_payment_elec_hd
-- (
-- flat_id
-- ,`year_month`
-- ,activation
-- ,created_at
-- ,created_by
-- )
-- values
-- (
-- i_flat_id
-- ,i_year_month
-- ,1
-- ,CURRENT_TIMESTAMP()
-- ,i_user_id
-- 	);
	-- insert vào Header
	SELECT id from tf_payment_elec_hd where flat_id = i_flat_id and `year_month` = i_year_month and activation = 1 into v_elec_hd_id;
	-- Update
	
	Update tf_payment_elec_hd u, tenement_flats a, tenement_elec_types b, tf_elec_used c
	Set 
		u.elec_type_id = a.elec_type_id
		,u.elec_type_name = b.elec_type
		,u.elec_used_id = c.id
		,u.date_from = c.date_from
		,u.date_to = c.date_to
		,u.old_index_hd  = c.old_index
		,u.new_index_hd = c.new_index
		,u.mount_hd = c.new_index - c.old_index
		,u.comment_hd = c.`comment`
	WHERE 
		a.tenement_id = b.tenement_id and
		a.id = c.flat_id and
		a.id = i_flat_id and
		c.`year_month` = i_year_month and
		a.activation = 1 and
		b.activation = 1 and
		c.activation = 1 and
		a.elec_type_id = b.id and
		u.id = v_elec_hd_id;
		
	-- INSERT INTO tf_payment_elec_hd
-- (
-- flat_id
-- ,elec_type_id
-- ,elec_type_name
-- ,elec_used_id
-- ,`year_month`
-- ,date_from
-- ,date_to
-- ,old_index_hd
-- ,new_index_hd
-- ,mount_hd
-- ,comment_hd
-- ,activation
-- ,created_at
-- ,created_by
-- )
-- SELECT
-- i_flat_id,
-- b.id,
-- b.elec_type,
-- c.id,
-- i_year_month,
-- c.date_from,
-- c.date_to,
-- c.old_index,
-- c.new_index,
-- c.new_index - c.old_index,
-- c.`comment`,
-- 1,
-- CURRENT_TIMESTAMP(),
-- i_user_id
-- FROM tenement_flats a, tenement_elec_types b, tf_elec_used c
-- WHERE
-- a.tenement_id = b.tenement_id and
-- a.id = c.flat_id and
-- a.id = i_flat_id and
-- c.`year_month` = i_year_month and
-- a.activation = 1 and
-- b.activation = 1 and
-- c.activation = 1 and
-- 		a.elec_type_id = b.id;

	-- SELECT LAST_INSERT_ID() into v_elec_hd_id;
	
	SET v_row_no = 0;
	
	OPEN elec_tariff_cur;	
	read_loop: LOOP
	FETCH elec_tariff_cur 
	INTO v_elec_type_id, v_elec_type, v_name, v_index_from, v_price, v_other_fee01, v_other_fee02, v_vat;
		IF done THEN
			LEAVE read_loop;
		END IF;
		
		-- Select v_used;
		IF v_used >= v_index_from THEN
			-- SELECT v_elec_type_id, v_elec_type, v_name, v_used ,v_index_from , v_used - v_index_from , v_price, v_other_fee01, v_other_fee02, v_vat;			
			SET v_used_temp = v_used - v_index_from;
			SET v_other_fee01_money = v_price * v_used_temp * ifnull(v_other_fee01, 0)/100; 
			SET v_other_fee02_money = v_price * v_used_temp * ifnull(v_other_fee02, 0)/100; 
			SET v_vat_money = v_price * v_used_temp * ifnull(v_vat, 0)/100; 
	 
	 		SET v_total_money = v_price * v_used_temp + v_other_fee01_money +  v_other_fee02_money + v_vat_money;
	 		
	 		-- Insert vao phan detail
	 		INSERT INTO tf_payment_elec_dt(
	 							 row_no
								,elec_hd_id
								,elec_tariff_id
								,elec_tariff_name
								,`year_month`
								,from_index
								,to_index
								,mount
								,vat
								,vat_money
								,other_fee01
								,other_fee01_money
								,other_fee02
								,other_fee02_money
								,price
								,total
								,`comment`
								,activation
								,created_at
								,updated_at
								,created_by
								,updated_by
				)
				values(
								v_row_no,
								v_elec_hd_id,
								v_elec_type,
								v_name,
								i_year_month,
								v_index_from,
								v_used,
								v_used_temp,
								v_vat,
								v_vat_money,
								v_other_fee01,
								v_other_fee01_money,
								v_other_fee02,
								v_other_fee02_money,
								v_price,
								v_total_money,
								'',
								1,
								CURRENT_TIMESTAMP(),
								CURRENT_TIMESTAMP(),
								i_user_id,
								i_user_id
				);
	 		-- SELECT v_used_temp, v_price, v_other_fee01_money , v_other_fee02_money , v_vat_money, v_total_money;
			SET v_used = v_index_from;
			SET v_row_no = v_row_no + 1;
			
		END IF;
	END LOOP;
	
	CLOSE elec_tariff_cur;

	Update tf_payment_elec_hd a,
	(
	select 
		a.elec_hd_id, 
		sum(ifnull(a.mount,0)) mount, 
		sum(ifnull(a.vat_money,0)) vat_money, 
		sum(ifnull(a.other_fee01_money,0)) other_fee01_money, 
		sum(ifnull(a.other_fee02_money,0)) other_fee02_money, 
		sum(ifnull(a.total,0)) total
	from tf_payment_elec_dt a
	where 
		a.activation = 1 and a.elec_hd_id = v_elec_hd_id
	group by a.elec_hd_id) as b
	set 
		a.mount_hd = b.mount,
		a.vat_money_hd = b.vat_money,
		a.other_fee01_money_hd = b.other_fee01_money,
		a.other_fee02_money_hd = b.other_fee02_money,
		a.total_hd = b.total
	where a.id = b.elec_hd_id and b.elec_hd_id = v_elec_hd_id;
END