-- Khoi tao
update tenement_flats_tmp b
set b.TraPQLT2 = 0, b.TraPQLT1 = 0, b.TraNuocT2 = 0

-- Update phi da tra cho PQL T1
update tf_payment_all_months a, tenement_flats_tmp b
set b.TraPQLT1 = a.manager_fee
where a.flat_id = b.id and a.`year_month` = '201701' and b.Month1_2 > 0

-- Update phi da tra cho PQL T1 nếu tiền đã trả < hơn PQL thì lấy tiền đã trả
update tenement_flats_tmp b
set b.TraPQLT1 = b.Month1_2
where b.Month1_2 < b.TraPQLT1 

-- Update phi da tra cho PQL2 Tổng trừ cho PQLT1 nếu > 0
update tenement_flats_tmp b
set b.TraPQLT2 = b.Month1_2 - b.TraPQLT1
where b.Month1_2 - b.TraPQLT1 >= 0

-- Update Phi da tra PQL2 nếu phát sinh tháng <= Trả PQL2
update tf_payment_all_months a, tenement_flats_tmp b
set b.TraPQLT2 = a.manager_fee
where a.flat_id = b.id and a.`year_month` = '201702' and b.TraPQLT2 > a.manager_fee

-- Update Phí đã trả cho Nước
update tenement_flats_tmp b
set b.TraNuocT2 = b.Month1_2 - b.TraPQLT1 - b.TraPQLT2
where b.Month1_2 - b.TraPQLT1 - b.TraPQLT2 >= 0

-- Update Phí đã trả Nước
update tf_payment_all_months a, tenement_flats_tmp b
set b.TraNuocT2 = a.water_fee
where a.flat_id = b.id and a.`year_month` = '201702' and b.TraNuocT2 > a.water_fee

-- Update trả phí
update tf_payment_all_months a, tenement_flats_tmp b
set a.manager_fee_paid = b.TraPQLT1
where a.flat_id = b.id and a.`year_month` = '201701'

update tf_payment_all_months a, tenement_flats_tmp b
set a.manager_fee_paid = b.TraPQLT2, a.water_fee_paid = b.TraNuocT2
where a.flat_id = b.id and a.`year_month` = '201702'
