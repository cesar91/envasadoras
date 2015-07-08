
// --------- ANTICIPO
-- BUSINESS_UNIT = GMDEM | VCHR_BLD_KEY_C1 = 101365 | VOUCHER_ID = NEXT | VOUCHER_STYLE = PPAY | INVOICE_ID = 101365 | INVOICE_DT = 18-May-2011 | VENDOR_ID = 0000010869 | ORIGIN = EXP | ACCOUNTING_DT = 18-May-2011 | GROSS_AMT = 210.5 | TAX_EXEMPT = N | TXN_CURRENCY_CD = USD | MATCH_ACTION = Y | PREPAID_REF = 101365 | PREPAID_AUTO_APPLY = N | IN_PROCESS_FLG = N | IMAGE_DATE = 18-May-2011 | INSPECT_DT = 18-May-2011 | INV_RECPT_DT = 18-May-2011 | RECEIPT_DT = 18-May-2011 | DSCNT_DUE_DT = 18-May-2011 | DUE_DT = 18-May-2011 | 
INSERT INTO PS_MTI_VCHD_AP_TBL ( BUSINESS_UNIT,VCHR_BLD_KEY_C1,VCHR_BLD_KEY_C2,VCHR_BLD_KEY_N1,VCHR_BLD_KEY_N2,VOUCHER_ID,VOUCHER_STYLE,INVOICE_ID,INVOICE_DT,VENDOR_SETID,VENDOR_ID,VNDR_LOC,ADDRESS_SEQ_NUM,GRP_AP_ID,ORIGIN,OPRID,ACCOUNTING_DT,POST_VOUCHER,DST_CNTRL_ID,VOUCHER_ID_RELATED,GROSS_AMT,DSCNT_AMT,TAX_EXEMPT,SALETX_AMT,FREIGHT_AMT,MISC_AMT,PYMNT_TERMS_CD,ENTERED_DT,TXN_CURRENCY_CD,RT_TYPE,RATE_MULT,RATE_DIV,VAT_ENTRD_AMT,MATCH_ACTION,CUR_RT_SOURCE,DSCNT_AMT_FLG,DUE_DT_FLG,VCHR_APPRVL_FLG,BUSPROCNAME,APPR_RULE_SET,VAT_DCLRTN_POINT,VAT_CALC_TYPE,VAT_CALC_GROSS_NET,VAT_RECALC_FLG,VAT_CALC_FRGHT_FLG,VAT_TREATMENT_GRP,COUNTRY_SHIP_FROM,STATE_SHIP_FROM,COUNTRY_SHIP_TO,STATE_SHIP_TO,COUNTRY_VAT_BILLFR,COUNTRY_VAT_BILLTO,VAT_EXCPTN_CERTIF,VAT_ROUND_RULE,COUNTRY_LOC_SELLER,STATE_LOC_SELLER,COUNTRY_LOC_BUYER,STATE_LOC_BUYER,COUNTRY_VAT_SUPPLY,STATE_VAT_SUPPLY,COUNTRY_VAT_PERFRM,STATE_VAT_PERFRM,STATE_VAT_DEFAULT,PREPAID_REF,PREPAID_AUTO_APPLY,DESCR254_MIXED,EIN_FEDERAL,EIN_STATE_LOCAL,PROCESS_INSTANCE,IN_PROCESS_FLG,BUSINESS_UNIT_PO,PO_ID,PACKSLIP_NO,PAY_TRM_BSE_DT_OPT,VAT_CALC_MISC_FLG,IMAGE_REF_ID,IMAGE_DATE,PAY_SCHEDULE_TYPE,TAX_GRP,TAX_PYMNT_TYPE,INSPECT_DT,INV_RECPT_DT,RECEIPT_DT,BILL_OF_LADING,CARRIER_ID,DOC_TYPE,DSCNT_DUE_DT,DSCNT_PRORATE_FLG,DUE_DT,ECQUEUEINSTANCE,ECTRANSID,FRGHT_CHARGE_CODE,LC_ID,MISC_CHARGE_CODE,REMIT_ADDR_SEQ_NUM,SALETX_CHARGE_CODE,VCHR_BLD_CODE,BUSINESS_UNIT_AR,CUST_ID,ITEM,ITEM_LINE,VCHR_SRC,VAT_EXCPTN_TYPE,USER_VCHR_CHAR1,USER_VCHR_CHAR2,USER_VCHR_DEC,USER_VCHR_DATE,USER_VCHR_NUM1,USER_HDR_CHAR1,LASTUPDOPRID,LASTUPDDTTM,OPRID_ENTERED_BY,CREATE_DTTM,PROCESSED_FLG ) VALUES ( 'GMDEM',101365,' ',0,0.00,'NEXT','PPAY',101365,'18-May-2011',' ','0000010869',' ',0,' ','EXP',' ','18-May-2011',' ',' ',' ',210.5,0.00,'N',0.00,0.00,0.00,' ',SYSDATE,'USD',' ',0.00,0.00,0.00,'Y',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',101365,'N',' ',' ',' ',0.00,'N',' ',' ',' ',' ',' ',' ','18-May-2011',' ',' ',' ','18-May-2011','18-May-2011','18-May-2011',' ',' ',' ','18-May-2011',' ','18-May-2011',0,' ',' ',' ',' ',0,' ',' ',' ',' ',' ',0,' ',' ',' ',' ',0.00,SYSDATE,0,' ',' ',SYSDATE,'MASNGEXPENSES',SYSDATE,' ' );
-- BUSINESS_UNIT = GMDEM | VCHR_BLD_KEY_C1 = 101365 | VOUCHER_ID = NEXT | VOUCHER_LINE_NUM = 1 | LINE_NBR = 1 | DESCR = ANTICIPO DE VIAJE | MERCHANDISE_AMT = 210.5 | BUSINESS_UNIT_RECV = GMDEM | MATCH_LINE_OPT = E | DISTRIB_MTHD_FLG = A | SHIPTO_ID = CMART | VAT_APPLICABILITY = O | ADDR_SEQ_NUM_SHIP = 1 | VENDOR_ID = 0000010869 | DESCR254_MIXED = ANTICIPO | BUSINESS_UNIT_GL = GMDEM | ACCOUNT = 1014110103 | PRODUCT = DD0063 | DEPTID = DAD012 | TRANS_DT = 18-May-2011 | 
INSERT INTO PS_MTI_VCLN_AP_TBL ( BUSINESS_UNIT,VCHR_BLD_KEY_C1,VCHR_BLD_KEY_C2,VCHR_BLD_KEY_N1,VCHR_BLD_KEY_N2,VOUCHER_ID,VOUCHER_LINE_NUM,BUSINESS_UNIT_PO,PO_ID,LINE_NBR,SCHED_NBR,DESCR,MERCHANDISE_AMT,ITM_SETID,INV_ITEM_ID,QTY_VCHR,STATISTIC_AMOUNT,UNIT_OF_MEASURE,UNIT_PRICE,DSCNT_APPL_FLG,TAX_CD_VAT,BUSINESS_UNIT_RECV,RECEIVER_ID,RECV_LN_NBR,RECV_SHIP_SEQ_NBR,MATCH_LINE_OPT,DISTRIB_MTHD_FLG,SHIPTO_ID,SUT_BASE_ID,TAX_CD_SUT,ULTIMATE_USE_CD,SUT_EXCPTN_TYPE,SUT_EXCPTN_CERTIF,SUT_APPLICABILITY,VAT_APPLICABILITY,VAT_TXN_TYPE_CD,VAT_USE_ID,ADDR_SEQ_NUM_SHIP,BUS_UNIT_RELATED,VOUCHER_ID_RELATED,VENDOR_ID,VNDR_LOC,DESCR254_MIXED,SPEEDCHART_KEY,BUSINESS_UNIT_GL,ACCOUNT,ALTACCT,OPERATING_UNIT,PRODUCT,FUND_CODE,CLASS_FLD,PROGRAM_CODE,BUDGET_REF,AFFILIATE,AFFILIATE_INTRA1,AFFILIATE_INTRA2,CHARTFIELD1,CHARTFIELD2,CHARTFIELD3,DEPTID,PROJECT_ID,ECQUEUEINSTANCE,ECTRANSID,TAX_DSCNT_FLG,TAX_FRGHT_FLG,TAX_MISC_FLG,TAX_VAT_FLG,PHYSICAL_NATURE,VAT_RCRD_INPT_FLG,VAT_RCRD_OUTPT_FLG,VAT_TREATMENT,VAT_SVC_SUPPLY_FLG,VAT_SERVICE_TYPE,COUNTRY_LOC_BUYER,STATE_LOC_BUYER,COUNTRY_LOC_SELLER,STATE_LOC_SELLER,COUNTRY_VAT_SUPPLY,STATE_VAT_SUPPLY,COUNTRY_VAT_PERFRM,STATE_VAT_PERFRM,STATE_SHIP_FROM,STATE_VAT_DEFAULT,REQUESTOR_ID,VAT_ENTRD_AMT,VAT_RECEIPT,VAT_RGSTRN_SELLER,TRANS_DT,USER_VCHR_CHAR1,USER_VCHR_CHAR2,USER_VCHR_DEC,USER_VCHR_DATE,USER_VCHR_NUM1,USER_LINE_CHAR1,USER_SCHED_CHAR1,WTHD_SW,WTHD_CD,LASTUPDOPRID,LASTUPDDTTM,OPRID_ENTERED_BY,CREATED_DTTM,PROCESSED_FLG,ORIGIN ) VALUES ( 'GMDEM',101365,' ',0,0.00,'NEXT',1,' ',' ',1,0,'ANTICIPO DE VIAJE',210.5,' ',' ',0.00,0.00,' ',0.00,' ',' ','GMDEM',' ',0,0,'E','A','CMART',' ',' ',' ',' ',' ',' ','O',' ',' ',1,' ',' ','0000010869',' ','ANTICIPO',' ','GMDEM',1014110103,' ',' ','DD0063',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','DAD012',' ',0,' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',0.00,' ',' ','18-May-2011',' ',' ',0.00,SYSDATE,0,' ',' ',' ',' ',' ',SYSDATE,'MASNGEXPENSES',SYSDATE,' ',' ' );
-- BUSINESS_UNIT = GMDEM | VCHR_BLD_KEY_C1 = 101365 | VOUCHER_ID = NEXT | VOUCHER_LINE_NUM = 1 | DISTRIB_LINE_NUM = 1 | BUSINESS_UNIT_GL = GMDEM | ACCOUNT = 1014110103 | DEPTID = DAD012 | DESCR = ANTICIPO DE VIAJE | MERCHANDISE_AMT = 210.5 | PRODUCT = DD0063 | BUDGET_DT = 18-May-2011 | USER_VCHR_DATE = 18-May-2011 | CREATED_DTTM = 18-May-2011 | 
INSERT INTO PS_MTI_VCDS_AP_TBL ( BUSINESS_UNIT,VCHR_BLD_KEY_C1,VCHR_BLD_KEY_C2,VCHR_BLD_KEY_N1,VCHR_BLD_KEY_N2,VOUCHER_ID,VOUCHER_LINE_NUM,DISTRIB_LINE_NUM,BUSINESS_UNIT_GL,ACCOUNT,ALTACCT,DEPTID,STATISTICS_CODE,STATISTIC_AMOUNT,QTY_VCHR,DESCR,MERCHANDISE_AMT,BUSINESS_UNIT_PO,PO_ID,LINE_NBR,SCHED_NBR,PO_DIST_LINE_NUM,BUSINESS_UNIT_PC,ACTIVITY_ID,ANALYSIS_TYPE,RESOURCE_TYPE,RESOURCE_CATEGORY,RESOURCE_SUB_CAT,ASSET_FLG,BUSINESS_UNIT_AM,ASSET_ID,PROFILE_ID,COST_TYPE,VAT_TXN_TYPE_CD,BUSINESS_UNIT_RECV,RECEIVER_ID,RECV_LN_NBR,RECV_SHIP_SEQ_NBR,RECV_DIST_LINE_NUM,OPERATING_UNIT,PRODUCT,FUND_CODE,CLASS_FLD,PROGRAM_CODE,BUDGET_REF,AFFILIATE,AFFILIATE_INTRA1,AFFILIATE_INTRA2,CHARTFIELD1,CHARTFIELD2,CHARTFIELD3,PROJECT_ID,BUDGET_DT,ENTRY_EVENT,ECQUEUEINSTANCE,ECTRANSID,JRNL_LN_REF,VAT_APORT_CNTRL,USER_VCHR_CHAR1,USER_VCHR_CHAR2,USER_VCHR_DEC,USER_VCHR_DATE,USER_VCHR_NUM1,USER_DIST_CHAR1,OPEN_ITEM_KEY,VAT_RECOVERY_PCT,VAT_REBATE_PCT,VAT_CALC_AMT,VAT_BASIS_AMT,VAT_RCVRY_AMT,VAT_NRCVR_AMT,VAT_REBATE_AMT,VAT_TRANS_AMT,TAX_CD_VAT_PCT,VAT_INV_AMT,VAT_NONINV_AMT,LASTUPDOPRID,LASTUPDDTTM,OPRID_ENTERED_BY,CREATED_DTTM,PROCESSED_FLG,ORIGIN ) VALUES ( 'GMDEM',101365,' ',0,0.00,'NEXT',1,1,'GMDEM',1014110103,' ','DAD012',' ',0.00,0.00,'ANTICIPO DE VIAJE',210.5,' ',' ',0,0,0,' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',0,0,0,' ','DD0063',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','18-May-2011',' ',0,' ',' ',' ',' ',' ',0.00,'18-May-2011',0,' ',' ',0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,' ',SYSDATE,'MASNGEXPENSES','18-May-2011',' ',' ' );
// --------- COMPROBACION
-- BUSINESS_UNIT = GMDEM | VCHR_BLD_KEY_C1 = 101366 | VOUCHER_ID = NEXT | VOUCHER_STYLE = REG | INVOICE_ID = 101366 | INVOICE_DT = 18-May-2011 | VENDOR_ID = 0000010869 | ORIGIN = EXP | ACCOUNTING_DT = 18-May-2011 | GROSS_AMT = 210.5 | TAX_EXEMPT = N | TXN_CURRENCY_CD = USD | VAT_ENTRD_AMT = 29.04 | MATCH_ACTION = Y | VAT_DCLRTN_POINT = I | PREPAID_REF = 101365 | PREPAID_AUTO_APPLY = Y | IN_PROCESS_FLG = N | IMAGE_DATE = 18-May-2011 | INSPECT_DT = 18-May-2011 | INV_RECPT_DT = 18-May-2011 | RECEIPT_DT = 18-May-2011 | DSCNT_DUE_DT = 18-May-2011 | DUE_DT = 18-May-2011 | 
INSERT INTO PS_MTI_VCHD_AP_TBL ( BUSINESS_UNIT,VCHR_BLD_KEY_C1,VCHR_BLD_KEY_C2,VCHR_BLD_KEY_N1,VCHR_BLD_KEY_N2,VOUCHER_ID,VOUCHER_STYLE,INVOICE_ID,INVOICE_DT,VENDOR_SETID,VENDOR_ID,VNDR_LOC,ADDRESS_SEQ_NUM,GRP_AP_ID,ORIGIN,OPRID,ACCOUNTING_DT,POST_VOUCHER,DST_CNTRL_ID,VOUCHER_ID_RELATED,GROSS_AMT,DSCNT_AMT,TAX_EXEMPT,SALETX_AMT,FREIGHT_AMT,MISC_AMT,PYMNT_TERMS_CD,ENTERED_DT,TXN_CURRENCY_CD,RT_TYPE,RATE_MULT,RATE_DIV,VAT_ENTRD_AMT,MATCH_ACTION,CUR_RT_SOURCE,DSCNT_AMT_FLG,DUE_DT_FLG,VCHR_APPRVL_FLG,BUSPROCNAME,APPR_RULE_SET,VAT_DCLRTN_POINT,VAT_CALC_TYPE,VAT_CALC_GROSS_NET,VAT_RECALC_FLG,VAT_CALC_FRGHT_FLG,VAT_TREATMENT_GRP,COUNTRY_SHIP_FROM,STATE_SHIP_FROM,COUNTRY_SHIP_TO,STATE_SHIP_TO,COUNTRY_VAT_BILLFR,COUNTRY_VAT_BILLTO,VAT_EXCPTN_CERTIF,VAT_ROUND_RULE,COUNTRY_LOC_SELLER,STATE_LOC_SELLER,COUNTRY_LOC_BUYER,STATE_LOC_BUYER,COUNTRY_VAT_SUPPLY,STATE_VAT_SUPPLY,COUNTRY_VAT_PERFRM,STATE_VAT_PERFRM,STATE_VAT_DEFAULT,PREPAID_REF,PREPAID_AUTO_APPLY,DESCR254_MIXED,EIN_FEDERAL,EIN_STATE_LOCAL,PROCESS_INSTANCE,IN_PROCESS_FLG,BUSINESS_UNIT_PO,PO_ID,PACKSLIP_NO,PAY_TRM_BSE_DT_OPT,VAT_CALC_MISC_FLG,IMAGE_REF_ID,IMAGE_DATE,PAY_SCHEDULE_TYPE,TAX_GRP,TAX_PYMNT_TYPE,INSPECT_DT,INV_RECPT_DT,RECEIPT_DT,BILL_OF_LADING,CARRIER_ID,DOC_TYPE,DSCNT_DUE_DT,DSCNT_PRORATE_FLG,DUE_DT,ECQUEUEINSTANCE,ECTRANSID,FRGHT_CHARGE_CODE,LC_ID,MISC_CHARGE_CODE,REMIT_ADDR_SEQ_NUM,SALETX_CHARGE_CODE,VCHR_BLD_CODE,BUSINESS_UNIT_AR,CUST_ID,ITEM,ITEM_LINE,VCHR_SRC,VAT_EXCPTN_TYPE,USER_VCHR_CHAR1,USER_VCHR_CHAR2,USER_VCHR_DEC,USER_VCHR_DATE,USER_VCHR_NUM1,USER_HDR_CHAR1,LASTUPDOPRID,LASTUPDDTTM,OPRID_ENTERED_BY,CREATE_DTTM,PROCESSED_FLG ) VALUES ( 'GMDEM',101366,' ',0.00,0.00,'NEXT','REG',101366,'18-May-2011',' ','0000010869',' ',0,' ','EXP',' ','18-May-2011',' ',' ',' ',210.5,0.00,'N',0.00,0.00,0.00,' ',SYSDATE,'USD',' ',0.00,0.00,29.04,'Y',' ',' ',' ',' ',' ',' ','I',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',101365,'Y',' ',' ',' ',0.00,'N',' ',' ',' ',' ',' ',' ','18-May-2011',' ',' ',' ','18-May-2011','18-May-2011','18-May-2011',' ',' ',' ','18-May-2011',' ','18-May-2011',0,' ',' ',' ',' ',0,' ',' ',' ',' ',' ',0,' ',' ',' ',' ',0.00,SYSDATE,0,' ',' ',SYSDATE,'MASNGEXPENSES',SYSDATE,' ' );
-- BUSINESS_UNIT = GMDEM | VCHR_BLD_KEY_C1 = 101366 | VCHR_BLD_KEY_N1 = 101365 | VOUCHER_ID = NEXT | VOUCHER_LINE_NUM = 1 | LINE_NBR = 1 | DESCR = PASAJES | MERCHANDISE_AMT = 181.46 | TAX_CD_VAT = IVA 16% | BUSINESS_UNIT_RECV = GMDEM | MATCH_LINE_OPT = E | DISTRIB_MTHD_FLG = A | SHIPTO_ID = CMART | VAT_APPLICABILITY = T | ADDR_SEQ_NUM_SHIP = 1 | VENDOR_ID = 0000010869 | DESCR254_MIXED = COMPROBACION | BUSINESS_UNIT_GL = GMDEM | ACCOUNT = 6011110104 | PRODUCT = DD0063 | DEPTID = DAD012 | VAT_ENTRD_AMT = 29.04 | TRANS_DT = 18-May-2011 | 
INSERT INTO PS_MTI_VCLN_AP_TBL ( BUSINESS_UNIT,VCHR_BLD_KEY_C1,VCHR_BLD_KEY_C2,VCHR_BLD_KEY_N1,VCHR_BLD_KEY_N2,VOUCHER_ID,VOUCHER_LINE_NUM,BUSINESS_UNIT_PO,PO_ID,LINE_NBR,SCHED_NBR,DESCR,MERCHANDISE_AMT,ITM_SETID,INV_ITEM_ID,QTY_VCHR,STATISTIC_AMOUNT,UNIT_OF_MEASURE,UNIT_PRICE,DSCNT_APPL_FLG,TAX_CD_VAT,BUSINESS_UNIT_RECV,RECEIVER_ID,RECV_LN_NBR,RECV_SHIP_SEQ_NBR,MATCH_LINE_OPT,DISTRIB_MTHD_FLG,SHIPTO_ID,SUT_BASE_ID,TAX_CD_SUT,ULTIMATE_USE_CD,SUT_EXCPTN_TYPE,SUT_EXCPTN_CERTIF,SUT_APPLICABILITY,VAT_APPLICABILITY,VAT_TXN_TYPE_CD,VAT_USE_ID,ADDR_SEQ_NUM_SHIP,BUS_UNIT_RELATED,VOUCHER_ID_RELATED,VENDOR_ID,VNDR_LOC,DESCR254_MIXED,SPEEDCHART_KEY,BUSINESS_UNIT_GL,ACCOUNT,ALTACCT,OPERATING_UNIT,PRODUCT,FUND_CODE,CLASS_FLD,PROGRAM_CODE,BUDGET_REF,AFFILIATE,AFFILIATE_INTRA1,AFFILIATE_INTRA2,CHARTFIELD1,CHARTFIELD2,CHARTFIELD3,DEPTID,PROJECT_ID,ECQUEUEINSTANCE,ECTRANSID,TAX_DSCNT_FLG,TAX_FRGHT_FLG,TAX_MISC_FLG,TAX_VAT_FLG,PHYSICAL_NATURE,VAT_RCRD_INPT_FLG,VAT_RCRD_OUTPT_FLG,VAT_TREATMENT,VAT_SVC_SUPPLY_FLG,VAT_SERVICE_TYPE,COUNTRY_LOC_BUYER,STATE_LOC_BUYER,COUNTRY_LOC_SELLER,STATE_LOC_SELLER,COUNTRY_VAT_SUPPLY,STATE_VAT_SUPPLY,COUNTRY_VAT_PERFRM,STATE_VAT_PERFRM,STATE_SHIP_FROM,STATE_VAT_DEFAULT,REQUESTOR_ID,VAT_ENTRD_AMT,VAT_RECEIPT,VAT_RGSTRN_SELLER,TRANS_DT,USER_VCHR_CHAR1,USER_VCHR_CHAR2,USER_VCHR_DEC,USER_VCHR_DATE,USER_VCHR_NUM1,USER_LINE_CHAR1,USER_SCHED_CHAR1,WTHD_SW,WTHD_CD,LASTUPDOPRID,LASTUPDDTTM,OPRID_ENTERED_BY,CREATED_DTTM,PROCESSED_FLG,ORIGIN ) VALUES ( 'GMDEM',101366,' ',101365,0.00,'NEXT',1,' ',' ',1,0,'PASAJES',181.46,' ',' ',0.00,0.00,' ',0.00,' ','IVA 16%','GMDEM',' ',0,0,'E','A','CMART',' ',' ',' ',' ',' ',' ','T',' ',' ',1,' ',' ','0000010869',' ','COMPROBACION',' ','GMDEM',6011110104,' ',' ','DD0063',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','DAD012',' ',0,' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',29.04,' ',' ','18-May-2011',' ',' ',0.00,SYSDATE,0,' ',' ',' ',' ',' ',SYSDATE,'MASNGEXPENSES',SYSDATE,' ',' ' );
-- BUSINESS_UNIT = GMDEM | VCHR_BLD_KEY_C1 = 101366 | VCHR_BLD_KEY_N1 = 101365 | VOUCHER_ID = NEXT | VOUCHER_LINE_NUM = 1 | DISTRIB_LINE_NUM = 1 | BUSINESS_UNIT_GL = GMDEM | ACCOUNT = 6011110104 | DEPTID = DAD012 | DESCR = PASAJES | MERCHANDISE_AMT = 181.46 | PRODUCT = DD0063 | BUDGET_DT = 18-May-2011 | USER_VCHR_DATE = 18-May-2011 | CREATED_DTTM = 18-May-2011 | 
INSERT INTO PS_MTI_VCDS_AP_TBL ( BUSINESS_UNIT,VCHR_BLD_KEY_C1,VCHR_BLD_KEY_C2,VCHR_BLD_KEY_N1,VCHR_BLD_KEY_N2,VOUCHER_ID,VOUCHER_LINE_NUM,DISTRIB_LINE_NUM,BUSINESS_UNIT_GL,ACCOUNT,ALTACCT,DEPTID,STATISTICS_CODE,STATISTIC_AMOUNT,QTY_VCHR,DESCR,MERCHANDISE_AMT,BUSINESS_UNIT_PO,PO_ID,LINE_NBR,SCHED_NBR,PO_DIST_LINE_NUM,BUSINESS_UNIT_PC,ACTIVITY_ID,ANALYSIS_TYPE,RESOURCE_TYPE,RESOURCE_CATEGORY,RESOURCE_SUB_CAT,ASSET_FLG,BUSINESS_UNIT_AM,ASSET_ID,PROFILE_ID,COST_TYPE,VAT_TXN_TYPE_CD,BUSINESS_UNIT_RECV,RECEIVER_ID,RECV_LN_NBR,RECV_SHIP_SEQ_NBR,RECV_DIST_LINE_NUM,OPERATING_UNIT,PRODUCT,FUND_CODE,CLASS_FLD,PROGRAM_CODE,BUDGET_REF,AFFILIATE,AFFILIATE_INTRA1,AFFILIATE_INTRA2,CHARTFIELD1,CHARTFIELD2,CHARTFIELD3,PROJECT_ID,BUDGET_DT,ENTRY_EVENT,ECQUEUEINSTANCE,ECTRANSID,JRNL_LN_REF,VAT_APORT_CNTRL,USER_VCHR_CHAR1,USER_VCHR_CHAR2,USER_VCHR_DEC,USER_VCHR_DATE,USER_VCHR_NUM1,USER_DIST_CHAR1,OPEN_ITEM_KEY,VAT_RECOVERY_PCT,VAT_REBATE_PCT,VAT_CALC_AMT,VAT_BASIS_AMT,VAT_RCVRY_AMT,VAT_NRCVR_AMT,VAT_REBATE_AMT,VAT_TRANS_AMT,TAX_CD_VAT_PCT,VAT_INV_AMT,VAT_NONINV_AMT,LASTUPDOPRID,LASTUPDDTTM,OPRID_ENTERED_BY,CREATED_DTTM,PROCESSED_FLG,ORIGIN ) VALUES ( 'GMDEM',101366,' ',101365,0.00,'NEXT',1,1,'GMDEM',6011110104,' ','DAD012',' ',0.00,0.00,'PASAJES',181.46,' ',' ',0,0,0,' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',0,0,0,' ','DD0063',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','18-May-2011',' ',0,' ',' ',' ',' ',' ',0.00,'18-May-2011',0,' ',' ',0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,' ',SYSDATE,'MASNGEXPENSES','18-May-2011',' ',' ' );
commit;