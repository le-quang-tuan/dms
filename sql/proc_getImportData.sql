BEGIN
	if iType = 'gas' then
		INSERT INTO tf_gas_used (flat_id, `year_month`, date_from, date_to, old_index, new_index, `comment`, activation, created_at, updated_at)
		SELECT flat_id, `year_month`, date_from, date_to, old_index, new_index, `comment`, activation, created_at, updated_at
		FROM tf_gas_used_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken;
		
		DELETE 
		FROM tf_gas_used_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken; 
	elseif iType = 'elec' then
		INSERT INTO tf_elec_used (flat_id, `year_month`, date_from, date_to, old_index, new_index, `comment`, activation, created_at, updated_at)
		SELECT flat_id, `year_month`, date_from, date_to, old_index, new_index, `comment`, activation, created_at, updated_at
		FROM tf_elec_used_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken;
		
		DELETE 
		FROM tf_elec_used_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken; 
	elseif iType = 'water' then
		INSERT INTO tf_water_used (flat_id, `year_month`, date_from, date_to, old_index, new_index, `comment`, activation, created_at, updated_at)
		SELECT flat_id, `year_month`, date_from, date_to, old_index, new_index, `comment`, activation, created_at, updated_at
		FROM tf_water_used_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken;
		
		DELETE 
		FROM tf_water_used_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken; 
	elseif iType = 'service' then
		INSERT INTO tf_service_used (flat_id, `year_month`, date_from, date_to, name, mount, unit, price, `comment`, activation, created_at, updated_at)
		SELECT flat_id, `year_month`, date_from, date_to, name, mount, unit, price, `comment`, activation, created_at, updated_at
		FROM tf_service_used_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken;
		
		DELETE 
		FROM tf_service_used_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken; 
	elseif iType = 'vehicle' then
		INSERT INTO tf_vehicle (flat_id, begin_contract_date, end_contract_date, name, label, maker, color, vehicle_type_id, `comment`, activation, created_at, updated_at)
		SELECT flat_id, begin_contract_date, end_contract_date, name, label, maker, color, vehicle_type_id, `comment`, activation, created_at, updated_at
		FROM tf_vehicle_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken;
		
		DELETE 
		FROM tf_vehicle_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken; 
	elseif iType = 'flat' then
		Update tenement_flats_import a, tenement_elec_types b
		Set a.elec_type_id = b.id
		where b.activation = 1 and a.tenement_id = b.tenement_id and a.elec_code = b.elec_code and a.tenement_id = iTenement_id and a.token = iToken;
		
		Update tenement_flats_import a, tenement_water_types b
		Set a.water_type_id = b.id
		where b.activation = 1 and a.tenement_id = b.tenement_id and a.water_code = b.water_code and a.tenement_id = iTenement_id and a.token = iToken;
		
		Update tenement_flats_import a, tenement_gas_types b
		Set a.gas_type_id = b.id
		where b.activation = 1 and a.tenement_id = b.tenement_id and a.gas_code = b.gas_code and a.tenement_id = iTenement_id and a.token = iToken;

		INSERT INTO tenement_flats (address, tenement_id, flat_code, block_name, block_num, `floor`, flat_num, name, phone, `area`, persons, receive_date, elec_type_id, water_type_id, gas_type_id, `comment`, activation, created_at, updated_at)
		SELECT address, tenement_id, flat_code, block_name, block_num, `floor`, flat_num, name, phone, `area`, persons, receive_date, elec_type_id, water_type_id, gas_type_id, `comment`, activation, created_at, updated_at
		FROM tenement_flats_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken;
		
		DELETE 
		FROM tenement_flats_import
		WHERE tenement_id = iTenement_id
		-- AND `year_month` = iYearMonth
		AND token = iToken; 
	end if;
END