CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_payment_month`(IN `i_tenement_id` INT, IN `i_year_month` INT, IN `i_flat_id` INT, IN `i_user_id` INT)
	LANGUAGE SQL
	NOT DETERMINISTIC
	CONTAINS SQL
	SQL SECURITY DEFINER
	COMMENT ''
BEGIN
	DECLARE done INT DEFAULT FALSE;
	DECLARE v_flat_id INT;
	DECLARE v_his_id INT;
	DECLARE v_count INT;
	
	--  lấy danh sách các biểu phí và sắp xếp theo chế độ chỉ số thấp dần
	DECLARE flats_cur CURSOR FOR		
		SELECT a.id
		FROM tenement_flats a
		WHERE 
			a.tenement_id = i_tenement_id AND
			a.activation = 1 AND
			(case when i_flat_id is null or i_flat_id = '' then 1=1 else a.id = i_flat_id end)
		ORDER BY a.id;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	-- insert log thuc hien ket so
	Insert into tf_payment_his
	(
		`year_month`,
		`tenement_id`,
		`flat_count`,
		`flat_count_finish`,
		`begin_at`,
		`finish_at`,
		`activation`,
		`created_at`,
		`updated_at`,
		`created_by`,
		`updated_by`
	)
	values
	(
		i_year_month,
		i_tenement_id,
		(Select count(*) from tenement_flats where tenement_id = i_tenement_id and activation = 1),
		0,
		CURRENT_TIMESTAMP(),
		null,
		1,
		CURRENT_TIMESTAMP(),
		null,
		i_user_id,
		i_user_id
	);
	SELECT LAST_INSERT_ID() into v_his_id;
	
	set v_count = 0;
	-- update tất cả các phiếu đã tạo thành 0
	update tf_payment_all_months a, tenement_flats b
	set a.activation = 0
	where a.flat_id = b.id and 
	b.tenement_id = i_tenement_id and 
	a.year_month = i_year_month and 
	(case when i_flat_id is null or i_flat_id = '' then 1=1 else a.id = i_flat_id end);

	-- update tất cả các phiếu đã tạo thành 0
	update tf_payment_elec_hd a, tenement_flats b
	set a.activation = 0
	where a.flat_id = b.id and 
	b.tenement_id = i_tenement_id and 
	a.year_month = i_year_month and 
	(case when i_flat_id is null or i_flat_id = '' then 1=1 else a.id = i_flat_id end);

	update tf_payment_water_hd a, tenement_flats b
	set a.activation = 0
	where a.flat_id = b.id and 
	b.tenement_id = i_tenement_id and 
	a.year_month = i_year_month and 
	(case when i_flat_id is null or i_flat_id = '' then 1=1 else a.id = i_flat_id end);

	update tf_payment_gas_hd a, tenement_flats b
	set a.activation = 0
	where a.flat_id = b.id and 
	b.tenement_id = i_tenement_id and 
	a.year_month = i_year_month and 
	(case when i_flat_id is null or i_flat_id = '' then 1=1 else a.id = i_flat_id end);

	update tf_payment_parking_hd a, tenement_flats b
	set a.activation = 0
	where a.flat_id = b.id and 
	b.tenement_id = i_tenement_id and 
	a.year_month = i_year_month and 
	(case when i_flat_id is null or i_flat_id = '' then 1=1 else a.id = i_flat_id end);

	update tf_payment_service_hd a, tenement_flats b
	set a.activation = 0
	where a.flat_id = b.id and 
	b.tenement_id = i_tenement_id and 
	a.year_month = i_year_month and 
	(case when i_flat_id is null or i_flat_id = '' then 1=1 else a.id = i_flat_id end);

	-- insert tất cả các flat vào table
	insert into tf_payment_all_months 
	(tenement_id, `year_month`, flat_id, activation, created_at, created_by, updated_at, updated_by)
	Select i_tenement_id, i_year_month, id, 1, CURRENT_TIMESTAMP(), i_user_id, CURRENT_TIMESTAMP(), i_user_id from
	tenement_flats
	where activation = 1 and tenement_id = i_tenement_id and (case when i_flat_id is null or i_flat_id = '' then 1=1 else id = i_flat_id end); 

	insert into tf_payment_elec_hd
	(tenement_id, `year_month`, flat_id, activation, created_at, created_by, updated_at, updated_by)
	Select i_tenement_id, i_year_month, id, 1, CURRENT_TIMESTAMP(), i_user_id, CURRENT_TIMESTAMP(), i_user_id from
	tenement_flats
	where activation = 1 and tenement_id = i_tenement_id and (case when i_flat_id is null or i_flat_id = '' then 1=1 else id = i_flat_id end); 

	insert into tf_payment_water_hd
	(tenement_id, `year_month`, flat_id, activation, created_at, created_by, updated_at, updated_by)
	Select i_tenement_id, i_year_month, id, 1, CURRENT_TIMESTAMP(), i_user_id, CURRENT_TIMESTAMP(), i_user_id from
	tenement_flats
	where activation = 1 and tenement_id = i_tenement_id and (case when i_flat_id is null or i_flat_id = '' then 1=1 else id = i_flat_id end); 

	insert into tf_payment_gas_hd
	(tenement_id, `year_month`, flat_id, activation, created_at, created_by, updated_at, updated_by)
	Select i_tenement_id, i_year_month, id, 1, CURRENT_TIMESTAMP(), i_user_id, CURRENT_TIMESTAMP(), i_user_id from
	tenement_flats
	where activation = 1 and tenement_id = i_tenement_id and (case when i_flat_id is null or i_flat_id = '' then 1=1 else id = i_flat_id end); 

	insert into tf_payment_parking_hd
	(tenement_id, `year_month`, flat_id, activation, created_at, created_by, updated_at, updated_by)
	Select i_tenement_id, i_year_month, id, 1, CURRENT_TIMESTAMP(), i_user_id, CURRENT_TIMESTAMP(), i_user_id from
	tenement_flats
	where activation = 1 and tenement_id = i_tenement_id and (case when i_flat_id is null or i_flat_id = '' then 1=1 else id = i_flat_id end); 

	insert into tf_payment_service_hd
	(tenement_id, `year_month`, flat_id, activation, created_at, created_by, updated_at, updated_by)
	Select i_tenement_id, i_year_month, id, 1, CURRENT_TIMESTAMP(), i_user_id, CURRENT_TIMESTAMP(), i_user_id from
	tenement_flats
	where activation = 1 and tenement_id = i_tenement_id and (case when i_flat_id is null or i_flat_id = '' then 1=1 else id = i_flat_id end); 


	OPEN flats_cur;	
	read_loop: LOOP
	FETCH flats_cur 
	INTO v_flat_id;
		IF done THEN
			LEAVE read_loop;
		END IF;
		
		set v_count = v_count + 1;
		
		call proc_payment_manager_fee(i_year_month, v_flat_id, i_user_id);
		call proc_payment_elec_fee(i_year_month, v_flat_id, i_user_id);
		call proc_payment_water_fee(i_year_month, v_flat_id, i_user_id);
		call proc_payment_gas_fee(i_year_month, v_flat_id, i_user_id); 
		call proc_payment_parking_fee(i_year_month, v_flat_id, i_user_id);
		call proc_payment_service_fee(i_year_month, v_flat_id, i_user_id);	
		
		-- Update tong cac phi phat sinh trong thang
		-- update tf_payment_all_months
		-- set 
		-- where flat_id = v_flat_id and `year_month` = i_year_month and activation = 1;
		
		
		update tf_payment_his
		set flat_count_finish = v_count
		where id = v_his_id;
	END LOOP;
	
	CLOSE flats_cur;
	
	-- -------------------------- update dữ liệu
	Update tf_payment_elec_hd a,
	(
	select 
		a.elec_hd_id, 
		sum(ifnull(a.mount,0)) mount, 
		sum(ifnull(a.vat_money,0)) vat_money, 
		sum(ifnull(a.other_fee01_money,0)) other_fee01_money, 
		sum(ifnull(a.other_fee02_money,0)) other_fee02_money, 
		sum(ifnull(a.total,0)) total
	from tf_payment_elec_dt a, tf_payment_elec_hd b, tenement_flats c
	where 
		a.activation = 1 and a.elec_hd_id = b.id and b.`year_month` = i_year_month and b.activation = 1 and b.flat_id = c.id and c.tenement_id = i_tenement_id
	group by a.elec_hd_id) as b
	set 
		a.mount_hd = b.mount,
		a.vat_money_hd = b.vat_money,
		a.other_fee01_money_hd = b.other_fee01_money,
		a.other_fee02_money_hd = b.other_fee02_money,
		a.total_hd = b.total
	where a.id = b.elec_hd_id;

	Update tf_payment_water_hd a,
	(
	select 
		a.water_hd_id, 
		sum(ifnull(a.mount,0)) mount, 
		sum(ifnull(a.vat_money,0)) vat_money, 
		sum(ifnull(a.other_fee01_money,0)) other_fee01_money, 
		sum(ifnull(a.other_fee02_money,0)) other_fee02_money, 
		sum(ifnull(a.total,0)) total
	from tf_payment_water_dt a, tf_payment_water_hd b, tenement_flats c
	where 
		a.activation = 1 and a.water_hd_id = b.id and b.`year_month` = i_year_month and b.activation = 1 and b.flat_id = c.id and c.tenement_id = i_tenement_id
	group by a.water_hd_id) as b
	set 
		a.mount_hd = b.mount,
		a.vat_money_hd = b.vat_money,
		a.other_fee01_money_hd = b.other_fee01_money,
		a.other_fee02_money_hd = b.other_fee02_money,
		a.total_hd = b.total
	where a.id = b.water_hd_id;
	
	Update tf_payment_gas_hd a,
	(
	select 
		a.gas_hd_id, 
		sum(ifnull(a.mount,0)) mount, 
		sum(ifnull(a.vat_money,0)) vat_money, 
		sum(ifnull(a.other_fee01_money,0)) other_fee01_money, 
		sum(ifnull(a.other_fee02_money,0)) other_fee02_money, 
		sum(ifnull(a.total,0)) total
	from tf_payment_gas_dt a, tf_payment_gas_hd b, tenement_flats c
	where 
		a.activation = 1 and a.gas_hd_id = b.id and b.`year_month` = i_year_month and b.activation = 1 and b.flat_id = c.id and c.tenement_id = i_tenement_id
	group by a.gas_hd_id) as b
	set 
		a.mount_hd = b.mount,
		a.vat_money_hd = b.vat_money,
		a.other_fee01_money_hd = b.other_fee01_money,
		a.other_fee02_money_hd = b.other_fee02_money,
		a.total_hd = b.total
	where a.id = b.gas_hd_id and b.gas_hd_id;

	Update tf_payment_parking_hd a,
	(
	select 
		a.parking_hd_id, 
		sum(ifnull(a.total_money,0)) total_money
	from tf_payment_parking_dt a, tf_payment_parking_hd b, tenement_flats c
	where 
		a.activation = 1 and a.parking_hd_id = b.id and b.`year_month` = i_year_month and b.activation = 1 and b.flat_id = c.id and c.tenement_id = i_tenement_id
	group by a.parking_hd_id) as b
	set 
		a.total_money = b.total_money
	where a.id = b.parking_hd_id;
	
	Update tf_payment_service_hd a,
	(
	select 
		a.service_hd_id, 
		sum(ifnull(a.mount,0)) mount, 
		sum(ifnull(a.vat_money,0)) vat_money, 
		sum(ifnull(a.other_fee01_money,0)) other_fee01_money, 
		sum(ifnull(a.other_fee02_money,0)) other_fee02_money, 
		sum(ifnull(a.total_money,0)) total
	from tf_payment_service_dt a, tf_payment_service_hd b, tenement_flats c
	where 
		a.activation = 1 and a.service_hd_id = b.id and b.`year_month` = i_year_month and b.activation = 1 and b.flat_id = c.id and c.tenement_id = i_tenement_id
	group by a.service_hd_id) as b
	set 
		a.vat_money = b.vat_money,
		a.other_fee01_money = b.other_fee01_money,
		a.other_fee02_money = b.other_fee02_money,
		a.total_money = b.total
	where a.id = b.service_hd_id;
	
	-- update tất cả tiền và đã trả
	update tf_payment_all_months a, 
	(Select b.id,
	(Select ifnull(a.total_money,0) from tf_payment_manager a where a.activation = 1 and a.flat_id = b.id and a.`year_month` = i_year_month) manager_fee,
	(Select ifnull(a.total_hd,0) from tf_payment_elec_hd a where a.activation = 1 and a.flat_id = b.id and a.`year_month` = i_year_month ) elec_fee,
	(Select ifnull(a.total_hd,0)  from tf_payment_water_hd a where a.activation = 1 and a.flat_id = b.id and a.`year_month` = i_year_month ) water_fee,
	(Select ifnull(a.total_hd,0) from tf_payment_gas_hd a where a.activation = 1 and a.flat_id = b.id and a.`year_month` = i_year_month ) gas_fee,
	(Select ifnull(a.total_money,0) from tf_payment_parking_hd a where a.activation = 1 and a.flat_id = b.id and a.`year_month` = i_year_month ) parking_fee,
	(Select ifnull(a.total_money,0) from tf_payment_service_hd a where a.activation = 1 and a.flat_id = b.id and a.`year_month` = i_year_month ) service_fee
	from tenement_flats b) as b
	set 
		a.manager_fee = b.manager_fee,
		a.elec_fee = b.elec_fee,
		a.water_fee = b.water_fee,
		a.gas_fee = b.gas_fee,
		a.parking_fee = b.parking_fee,
		a.service_fee = b.service_fee
	where 
		a.flat_id = b.id 
		and a.`year_month` = i_year_month 
		and a.activation = 1
	;
	
	update tf_payment_all_months a, (
	Select a.id, a.`year_month`, 
	sum(a.manager_fee_paid) manager_fee_paid, 
	sum(a.elec_fee_paid) elec_fee_paid, 
	sum(a.water_fee_paid) water_fee_paid, 
	sum(a.gas_fee_paid) gas_fee_paid, 
	sum(a.parking_fee_paid) parking_fee_paid, 
	sum(a.service_fee_paid) service_fee_paid
	from (
	Select a.id, c.`year_month`
	, case when c.payment_type = 1 then sum(ifnull(c.money,0)) else 0  end as manager_fee_paid
	, case when c.payment_type = 2 then sum(ifnull(c.money,0)) else 0  end as elec_fee_paid
	, case when c.payment_type = 3 then sum(ifnull(c.money,0)) else 0  end as water_fee_paid
	, case when c.payment_type = 4 then sum(ifnull(c.money,0)) else 0  end as gas_fee_paid
	, case when c.payment_type = 5 then sum(ifnull(c.money,0)) else 0  end as parking_fee_paid
	, case when c.payment_type = 6 then sum(ifnull(c.money,0)) else 0  end as service_fee_paid
	
	from 
		tenement_flats a left outer join tf_paid_hd b 
		on a.id = b.flat_id and b.activation = 1
		left join tf_paid_dt c 
		on b.id = c.paid_id and c.activation = 1
	   and c.`year_month` = i_year_month
	where a.activation = 1
	group by a.id, c.`year_month`,c.payment_type) as a
	group by a.id, a.`year_month`) b
	Set 
		a.manager_fee_paid = b.manager_fee_paid,
		a.elec_fee_paid = b.elec_fee_paid,
		a.water_fee_paid = b.water_fee_paid,
		a.gas_fee_paid = b.gas_fee_paid,
		a.parking_fee_paid = b.parking_fee_paid,
		a.service_fee_paid = b.service_fee_paid
	where 
		a.flat_id = b.id 
		and a.`year_month` = i_year_month 
		and a.activation = 1
	;
	SET @rank=0;
	
	-- tạo bill phiếu thu
	Insert into tf_paybill_hd(	
		flat_id,
		bill_no,
		`year_month`,
		money,
		receive_date,
		receive_from,
		receiver,
		`comment`,
		paid_type,
		dept,
		office,
		book_bill,
		paymenter,
		reason,
		rate,
		address,
		paid_code,
		prepaid_flg,
		activation,
		created_at,
		updated_at,
		created_by,
		updated_by
	)
	Select 
		a.flat_id,
		@rank:=@rank+1 AS rank,
		a.`year_month`,
		0,
		'',
		'',
		'Cty Quản Lý BDS',
		a.`comment`,
		1,
	   c.office,
		c.office_address,
		concat(right(a.`year_month`,4) , c.tenement_code) ,
		'',
		concat('Phí dịch vụ quản lý tòa nhà', right(a.`year_month`,2)),
		0,
		b.flat_code,
		LEFT(UUID(), 8),
		0,
		1,
		a.created_at,
		a.updated_at,
		a.created_by,
		a.updated_by
	from tf_payment_all_months a, tenement_flats b, tenements c
	where a.flat_id = b.id and b.tenement_id = c.id and a.activation = 1 and a.`year_month` = i_year_month and c.id = i_tenement_id;
	
	-- insert detail bill
	insert into
tf_paybill_dt
(
	flat_id,
	payment_type,
	`year_month`,
	paybill_year_month,
	money,
	prepaid_flg,
	`comment`,
	activation,
	created_at,
	updated_at,
	created_by,
	updated_by
)
Select * from
(
Select 
a.flat_id,
1,
a.`year_month`,
i_year_month,
ifnull(a.manager_fee, 0) - ifnull(a.manager_fee_paid, 0),
0,
'Phí Quản Lý',
1 as paid_type,
a.created_at,
a.updated_at,
a.created_by,
a.updated_by
from 
tf_payment_all_months a, tenement_flats b, tenements c
where 
	a.flat_id = b.id and 
	b.tenement_id = c.id and 
	a.activation = 1 and 
	c.id = i_tenement_id and a.`year_month` <= i_year_month
	and
ifnull(a.manager_fee, 0) > ifnull(a.manager_fee_paid, 0)

union all

Select 
a.flat_id,
2,
a.`year_month`,
i_year_month,
ifnull(a.elec_fee, 0) - ifnull(a.elec_fee_paid, 0),
0,
'Phí Sử Dụng Điện',
1 as paid_type,
a.created_at,
a.updated_at,
a.created_by,
a.updated_by
from 
tf_payment_all_months a, tenement_flats b, tenements c
where 
	a.flat_id = b.id and 
	b.tenement_id = c.id and 
	a.activation = 1 and 
	c.id = i_tenement_id and a.`year_month` <= i_year_month
	and
ifnull(a.elec_fee, 0) > ifnull(a.elec_fee_paid, 0)

union all

Select 
a.flat_id,
3,
a.`year_month`,
i_year_month,
ifnull(a.water_fee, 0) - ifnull(a.water_fee_paid, 0),
0,
'Phí Sử Dụng Nước',
1 as paid_type,
a.created_at,
a.updated_at,
a.created_by,
a.updated_by
from 
tf_payment_all_months a, tenement_flats b, tenements c
where 
	a.flat_id = b.id and 
	b.tenement_id = c.id and 
	a.activation = 1 and 
	c.id = i_tenement_id and a.`year_month` <= i_year_month
	and
ifnull(a.water_fee, 0) > ifnull(a.water_fee_paid, 0)

union all

Select 
a.flat_id,
4,
a.`year_month`,
i_year_month,
ifnull(a.gas_fee, 0) - ifnull(a.gas_fee_paid, 0),
0,
'Phí Sử Dụng Gas',
1 as paid_type,
a.created_at,
a.updated_at,
a.created_by,
a.updated_by
from 
tf_payment_all_months a, tenement_flats b, tenements c
where 
	a.flat_id = b.id and 
	b.tenement_id = c.id and 
	a.activation = 1 and 
	c.id = i_tenement_id and a.`year_month` <= i_year_month
	and
ifnull(a.gas_fee, 0) > ifnull(a.gas_fee_paid, 0)

union all

Select 
a.flat_id,
5,
a.`year_month`,
i_year_month,
ifnull(a.parking_fee, 0) - ifnull(a.parking_fee_paid, 0),
0,
'Phí Gửi Xe Tháng',
1 as paid_type,
a.created_at,
a.updated_at,
a.created_by,
a.updated_by
from 
tf_payment_all_months a, tenement_flats b, tenements c
where 
	a.flat_id = b.id and 
	b.tenement_id = c.id and 
	a.activation = 1 and 
	c.id = i_tenement_id and a.`year_month` <= i_year_month
	and
ifnull(a.parking_fee, 0) > ifnull(a.parking_fee_paid, 0)

union all

Select 
a.flat_id,
6,
a.`year_month`,
i_year_month,
ifnull(a.service_fee, 0) - ifnull(a.service_fee_paid, 0),
0,
'Phí Khác',
1 as paid_type,
a.created_at,
a.updated_at,
a.created_by,
a.updated_by
from 
tf_payment_all_months a, tenement_flats b, tenements c
where 
	a.flat_id = b.id and 
	b.tenement_id = c.id and 
	a.activation = 1 and 
	c.id = i_tenement_id and a.`year_month` <= i_year_month
	and
ifnull(a.service_fee, 0) > ifnull(a.service_fee_paid, 0)) as temp;

-- update paybill id
update tf_paybill_dt a, tf_paybill_hd b, tenement_flats c, tenements d
Set a.paybill_id = b.id
Where a.flat_id = b.flat_id and a.`paybill_year_month` = b.`year_month` and a.activation =1 and b.activation = 1
and b.`year_month` = i_year_month and b.flat_id = c.id and c.tenement_id = d.id and d.id = i_tenement_id;

END