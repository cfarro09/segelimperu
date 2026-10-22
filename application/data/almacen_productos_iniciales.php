<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
return json_decode(<<<'CATALOGO_JSON'
[
  {
    "descripcion": "ACEITE ROJO PREMIO",
    "unidad": "FCO",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "13.00",
    "codigo": "ALM-0001"
  },
  {
    "descripcion": "ALCOHOL 70°",
    "unidad": "FCO",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "13.00",
    "codigo": "ALM-0002"
  },
  {
    "descripcion": "ALCOHOL EN GEL CON DISPENSADOR",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "18.00",
    "codigo": "ALM-0003"
  },
  {
    "descripcion": "ALCOHOL EN GEL REPUESTO",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0004"
  },
  {
    "descripcion": "ALCOHOL ISOPROPILICO",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "50.00",
    "codigo": "ALM-0005"
  },
  {
    "descripcion": "ALCOHOL ISOPROPILICO",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "25.00",
    "codigo": "ALM-0006"
  },
  {
    "descripcion": "ALCOHOL MEDICINAL",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "6.40",
    "codigo": "ALM-0007"
  },
  {
    "descripcion": "AMBIENTADOR PARA AUTO",
    "unidad": "UND",
    "marca": "GLADE",
    "categoria": "MATERIALES",
    "precio_valorado": "5.80",
    "codigo": "ALM-0008"
  },
  {
    "descripcion": "AROMA EN SPRAY",
    "unidad": "FCO",
    "marca": "GLADE",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0009"
  },
  {
    "descripcion": "AROMA EN SPRAY",
    "unidad": "FCO",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "7.00",
    "codigo": "ALM-0010"
  },
  {
    "descripcion": "AROMA LIQUIDO",
    "unidad": "GLN",
    "marca": "DARIZA",
    "categoria": "MATERIALES",
    "precio_valorado": "15.00",
    "codigo": "ALM-0011"
  },
  {
    "descripcion": "AROMA LIQUIDO",
    "unidad": "GLN",
    "marca": "LIMKIT",
    "categoria": "MATERIALES",
    "precio_valorado": "8.00",
    "codigo": "ALM-0012"
  },
  {
    "descripcion": "AROMA LIQUIDO",
    "unidad": "GLN",
    "marca": "POET",
    "categoria": "MATERIALES",
    "precio_valorado": "15.00",
    "codigo": "ALM-0013"
  },
  {
    "descripcion": "AROMA LIQUIDO",
    "unidad": "GLN",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "15.00",
    "codigo": "ALM-0014"
  },
  {
    "descripcion": "AROMA LIQUIDO",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "3.00",
    "codigo": "ALM-0015"
  },
  {
    "descripcion": "AROMA LIQUIDO",
    "unidad": "LTR",
    "marca": "POET",
    "categoria": "MATERIALES",
    "precio_valorado": "4.50",
    "codigo": "ALM-0016"
  },
  {
    "descripcion": "AROMA LIQUIDO",
    "unidad": "LTR",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0017"
  },
  {
    "descripcion": "BALDE 15 LTRS.",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "15.00",
    "codigo": "ALM-0018"
  },
  {
    "descripcion": "BALDE 17 LTRS.",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "16.00",
    "codigo": "ALM-0019"
  },
  {
    "descripcion": "BALDEADOR DE NYLON 40 CM",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "11.00",
    "codigo": "ALM-0020"
  },
  {
    "descripcion": "BALDEADOR DE NYLON 60 CM",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "30.00",
    "codigo": "ALM-0021"
  },
  {
    "descripcion": "BANCO DE 2 PASOS",
    "unidad": "UND",
    "marca": "DURAPLAST",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "45.00",
    "codigo": "ALM-0022"
  },
  {
    "descripcion": "BASE DE TRAPEADOR X 30 CM",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "12.00",
    "codigo": "ALM-0023"
  },
  {
    "descripcion": "BINNER QUITA MANCHAS",
    "unidad": "FCO",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "20.00",
    "codigo": "ALM-0024"
  },
  {
    "descripcion": "BOLSAS BLANCAS 18*26 (25 LTRS.)",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.03",
    "codigo": "ALM-0025"
  },
  {
    "descripcion": "BOLSAS BLANCAS 20*30",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.20",
    "codigo": "ALM-0026"
  },
  {
    "descripcion": "BOLSAS NEGRAS (180 LTRS.)",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.45",
    "codigo": "ALM-0027"
  },
  {
    "descripcion": "BOLSAS NEGRAS 18*26 (25 LTRS.)",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.09",
    "codigo": "ALM-0028"
  },
  {
    "descripcion": "BOLSAS NEGRAS 20*30",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.10",
    "codigo": "ALM-0029"
  },
  {
    "descripcion": "BOLSAS NEGRAS 26*40",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.15",
    "codigo": "ALM-0030"
  },
  {
    "descripcion": "BOLSAS NEGRAS 36*42 (140 LTRS.)",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.40",
    "codigo": "ALM-0031"
  },
  {
    "descripcion": "BOLSAS NEGRAS CON FUELLE (75 LTRS.)",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.20",
    "codigo": "ALM-0032"
  },
  {
    "descripcion": "BOLSAS NEGRAS CON FUELLE 50*60",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.90",
    "codigo": "ALM-0033"
  },
  {
    "descripcion": "BOLSAS NEGRAS X 240 LTRS",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.70",
    "codigo": "ALM-0034"
  },
  {
    "descripcion": "BOLSAS NEGRAS X 35 LTRS",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.18",
    "codigo": "ALM-0035"
  },
  {
    "descripcion": "BOLSAS ROJAS 180",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.45",
    "codigo": "ALM-0036"
  },
  {
    "descripcion": "BOLSAS ROJAS 18X26",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.15",
    "codigo": "ALM-0037"
  },
  {
    "descripcion": "BOLSAS ROJAS 20X30",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.15",
    "codigo": "ALM-0038"
  },
  {
    "descripcion": "BOLSAS ROJAS 26*40",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.25",
    "codigo": "ALM-0039"
  },
  {
    "descripcion": "BOTELLAS PLASTICAS 1/2 LTRS",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "1.00",
    "codigo": "ALM-0040"
  },
  {
    "descripcion": "BROCHA 4 PULGADAS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "11.50",
    "codigo": "ALM-0041"
  },
  {
    "descripcion": "BROCHA CHIQUITA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "5.00",
    "codigo": "ALM-0042"
  },
  {
    "descripcion": "CANASTILLA PARA URINARIO",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "8.00",
    "codigo": "ALM-0043"
  },
  {
    "descripcion": "CARRITOS MOPEROS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "300.00",
    "codigo": "ALM-0044"
  },
  {
    "descripcion": "CARRITOS TRANSPORTADORES IMPLEM",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "420.00",
    "codigo": "ALM-0045"
  },
  {
    "descripcion": "CERA AL AGUA",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "12.00",
    "codigo": "ALM-0046"
  },
  {
    "descripcion": "CERA AL AGUA AMARILLA",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "13.00",
    "codigo": "ALM-0047"
  },
  {
    "descripcion": "CERA AL AGUA NEGRA",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "13.00",
    "codigo": "ALM-0048"
  },
  {
    "descripcion": "CERA AL AGUA ROJA",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "13.00",
    "codigo": "ALM-0049"
  },
  {
    "descripcion": "CERA AUTOBRILLANTE",
    "unidad": "GLN",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "40.00",
    "codigo": "ALM-0050"
  },
  {
    "descripcion": "CERA AUTOBRILLANTE AMARILLO",
    "unidad": "SACHET",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0051"
  },
  {
    "descripcion": "CERA AUTOBRILLANTE NEGRO",
    "unidad": "SACHET",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0052"
  },
  {
    "descripcion": "CERA AUTOBRILLANTE NEUTRAL",
    "unidad": "SACHET",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0053"
  },
  {
    "descripcion": "CERA AUTOBRILLANTE ROJO",
    "unidad": "SACHET",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0054"
  },
  {
    "descripcion": "CERA EN PASTA AMARILLA",
    "unidad": "GLN",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "46.50",
    "codigo": "ALM-0055"
  },
  {
    "descripcion": "CERA EN PASTA MARRON",
    "unidad": "GLN",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "46.50",
    "codigo": "ALM-0056"
  },
  {
    "descripcion": "CERA EN PASTA NEGRA",
    "unidad": "GLN",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "46.50",
    "codigo": "ALM-0057"
  },
  {
    "descripcion": "CERA EN PASTA NEUTRAL",
    "unidad": "SACHET",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0058"
  },
  {
    "descripcion": "CERA EN PASTA NEUTRAL BLANCA",
    "unidad": "GLN",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "46.50",
    "codigo": "ALM-0059"
  },
  {
    "descripcion": "CERA EN PASTA ROJA",
    "unidad": "GLN",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "46.50",
    "codigo": "ALM-0060"
  },
  {
    "descripcion": "CERA EN PASTA ROJA",
    "unidad": "SACHET",
    "marca": "TEKNO",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0061"
  },
  {
    "descripcion": "CERA INTRA PREMIO BLANCA",
    "unidad": "FCO",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "11.50",
    "codigo": "ALM-0062"
  },
  {
    "descripcion": "CINTA DE SEGURIDAD AMARILLA",
    "unidad": "ROLLO",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "30.00",
    "codigo": "ALM-0063"
  },
  {
    "descripcion": "COMPU CLEANER PARA COMPUTADORAS",
    "unidad": "PQT",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "22.00",
    "codigo": "ALM-0064"
  },
  {
    "descripcion": "CONOS DE SEGURIDAD",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "27.00",
    "codigo": "ALM-0065"
  },
  {
    "descripcion": "DECAPANTE DE PISOS",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "90.00",
    "codigo": "ALM-0066"
  },
  {
    "descripcion": "DESATORADOR WC",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "3.50",
    "codigo": "ALM-0067"
  },
  {
    "descripcion": "DESENGRASANTE MR MUSCULO",
    "unidad": "FCO",
    "marca": "MR MUSCULO",
    "categoria": "MATERIALES",
    "precio_valorado": "16.00",
    "codigo": "ALM-0068"
  },
  {
    "descripcion": "DESENGRASANTE PROFESIONAL",
    "unidad": "GLN",
    "marca": "DARIZA",
    "categoria": "MATERIALES",
    "precio_valorado": "23.00",
    "codigo": "ALM-0069"
  },
  {
    "descripcion": "DESENGRASANTE REPUESTO X SACHET",
    "unidad": "UND",
    "marca": "MR MUSCULO",
    "categoria": "MATERIALES",
    "precio_valorado": "7.00",
    "codigo": "ALM-0070"
  },
  {
    "descripcion": "DESINFECTANTE PINO",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "7.00",
    "codigo": "ALM-0071"
  },
  {
    "descripcion": "DESINFECTANTE PINO",
    "unidad": "GLN",
    "marca": "PINESOL",
    "categoria": "MATERIALES",
    "precio_valorado": "38.00",
    "codigo": "ALM-0072"
  },
  {
    "descripcion": "DESINFECTANTE PINO",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "3.00",
    "codigo": "ALM-0073"
  },
  {
    "descripcion": "DETERGENTE",
    "unidad": "KLG",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "4.50",
    "codigo": "ALM-0074"
  },
  {
    "descripcion": "DETERGENTE",
    "unidad": "SACO",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "67.00",
    "codigo": "ALM-0075"
  },
  {
    "descripcion": "DETERGENTE EMBASADO",
    "unidad": "140",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "2.20",
    "codigo": "ALM-0076"
  },
  {
    "descripcion": "DISCOS ACTIVOS",
    "unidad": "UND",
    "marca": "PATO",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0077"
  },
  {
    "descripcion": "EMBASE ROTULADO SIN GATILLO",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "2.00",
    "codigo": "ALM-0078"
  },
  {
    "descripcion": "EMBUDO CHICO",
    "unidad": "UND",
    "marca": "REY",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "2.00",
    "codigo": "ALM-0079"
  },
  {
    "descripcion": "EMBUDO GRANDE",
    "unidad": "UND",
    "marca": "REY",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "4.00",
    "codigo": "ALM-0080"
  },
  {
    "descripcion": "ERIZO ESCOBILLON DE TECHO",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "37.00",
    "codigo": "ALM-0081"
  },
  {
    "descripcion": "ESCALERA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "0.00",
    "codigo": "ALM-0082"
  },
  {
    "descripcion": "ESCALERA DE 03 PASOS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "220.00",
    "codigo": "ALM-0083"
  },
  {
    "descripcion": "ESCALERA DE 04 PASOS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "260.00",
    "codigo": "ALM-0084"
  },
  {
    "descripcion": "ESCALERA DE 05 PASOS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "330.00",
    "codigo": "ALM-0085"
  },
  {
    "descripcion": "ESCALERA DE 08 PASOS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "470.00",
    "codigo": "ALM-0086"
  },
  {
    "descripcion": "ESCALERA DE 10 PASOS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "650.00",
    "codigo": "ALM-0087"
  },
  {
    "descripcion": "ESCALERA DE 12 PASOS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "900.00",
    "codigo": "ALM-0088"
  },
  {
    "descripcion": "ESCALERA TELESCOPICA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "1000.00",
    "codigo": "ALM-0089"
  },
  {
    "descripcion": "ESCOBA",
    "unidad": "UND",
    "marca": "PROLIMSA",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "11.00",
    "codigo": "ALM-0090"
  },
  {
    "descripcion": "ESCOBA BAJA POLICIA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "25.00",
    "codigo": "ALM-0091"
  },
  {
    "descripcion": "ESCOBA CHICA",
    "unidad": "UND",
    "marca": "CLORINDA",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "15.00",
    "codigo": "ALM-0092"
  },
  {
    "descripcion": "ESCOBILLA DE MANO C/ASA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "6.00",
    "codigo": "ALM-0093"
  },
  {
    "descripcion": "ESCOBILLA DE MANO S/ASA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "4.00",
    "codigo": "ALM-0094"
  },
  {
    "descripcion": "ESCOBILLA PARA LAV/MAQ",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "140.00",
    "codigo": "ALM-0095"
  },
  {
    "descripcion": "ESCOBILLON DE CERDAS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "26.00",
    "codigo": "ALM-0096"
  },
  {
    "descripcion": "ESCOBON",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "13.00",
    "codigo": "ALM-0097"
  },
  {
    "descripcion": "ESCOBON TIPO RELIMA 60 CM",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "26.00",
    "codigo": "ALM-0098"
  },
  {
    "descripcion": "ESPATULA 3\" METAL",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "5.00",
    "codigo": "ALM-0099"
  },
  {
    "descripcion": "ESPATULA 3\" PLASTICO",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "6.00",
    "codigo": "ALM-0100"
  },
  {
    "descripcion": "ESPONJA DE DUNLOPIO",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "1.00",
    "codigo": "ALM-0101"
  },
  {
    "descripcion": "ESPONJITA ACERO INOXIDABLE",
    "unidad": "UND",
    "marca": "LA CARMELITA",
    "categoria": "MATERIALES",
    "precio_valorado": "2.50",
    "codigo": "ALM-0102"
  },
  {
    "descripcion": "ESPONJITA VERDE",
    "unidad": "UND",
    "marca": "SCOTCH BRITE",
    "categoria": "MATERIALES",
    "precio_valorado": "1.50",
    "codigo": "ALM-0103"
  },
  {
    "descripcion": "ESPONJITA VERDE DOBLE USO (VERDE/AMARI)",
    "unidad": "UND",
    "marca": "SCOTCH BRITE",
    "categoria": "MATERIALES",
    "precio_valorado": "3.50",
    "codigo": "ALM-0104"
  },
  {
    "descripcion": "ESPONJITA VERDE LA MAQUINITA",
    "unidad": "UND",
    "marca": "SCOTCH BRITE",
    "categoria": "MATERIALES",
    "precio_valorado": "2.00",
    "codigo": "ALM-0105"
  },
  {
    "descripcion": "EXTENSION TELESCOPICA 2.5 MTRS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "80.00",
    "codigo": "ALM-0106"
  },
  {
    "descripcion": "EXTENSION TELESCOPICA GRANDE",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "150.00",
    "codigo": "ALM-0107"
  },
  {
    "descripcion": "FORWARD",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "70.00",
    "codigo": "ALM-0108"
  },
  {
    "descripcion": "FRANELA AMARILLA",
    "unidad": "MTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0109"
  },
  {
    "descripcion": "FRANELA ROJA",
    "unidad": "MTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0110"
  },
  {
    "descripcion": "GATILLOS CON PUNTA DE METAL",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0111"
  },
  {
    "descripcion": "GATILLOS IMPORTADOS PLASTICO",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "4.50",
    "codigo": "ALM-0112"
  },
  {
    "descripcion": "GUANTES CLINICOS ROJOS TALLA 7",
    "unidad": "PAR",
    "marca": "ETERNA",
    "categoria": "MATERIALES",
    "precio_valorado": "6.50",
    "codigo": "ALM-0113"
  },
  {
    "descripcion": "GUANTES CLINICOS ROJOS TALLA 8",
    "unidad": "PAR",
    "marca": "ETERNA",
    "categoria": "MATERIALES",
    "precio_valorado": "6.50",
    "codigo": "ALM-0114"
  },
  {
    "descripcion": "GUANTES CLINICOS ROJOS TALLA 9",
    "unidad": "PAR",
    "marca": "ETERNA",
    "categoria": "MATERIALES",
    "precio_valorado": "6.50",
    "codigo": "ALM-0115"
  },
  {
    "descripcion": "GUANTES DOMESTICOS TALLA 7",
    "unidad": "PAR",
    "marca": "ETERNA",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0116"
  },
  {
    "descripcion": "GUANTES DOMESTICOS TALLA 8",
    "unidad": "PAR",
    "marca": "ETERNA",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0117"
  },
  {
    "descripcion": "GUANTES DOMESTICOS TALLA 9",
    "unidad": "PAR",
    "marca": "ETERNA",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0118"
  },
  {
    "descripcion": "GUANTES LATEX",
    "unidad": "PAR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "1.00",
    "codigo": "ALM-0119"
  },
  {
    "descripcion": "GUANTES LATEX (VINILO)",
    "unidad": "CAJA",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "20.00",
    "codigo": "ALM-0120"
  },
  {
    "descripcion": "GUANTES NEGROS Nº 7 CALIBRE 25",
    "unidad": "PAR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0121"
  },
  {
    "descripcion": "GUANTES NEGROS Nº 8 CALIBRE 25",
    "unidad": "PAR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0122"
  },
  {
    "descripcion": "GUANTES NEGROS Nº 9 CALIBRE 25",
    "unidad": "PAR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0123"
  },
  {
    "descripcion": "GUANTES TAPA CODOS",
    "unidad": "PAR",
    "marca": "CHEMICAL",
    "categoria": "MATERIALES",
    "precio_valorado": "20.00",
    "codigo": "ALM-0124"
  },
  {
    "descripcion": "HISOPO DE WC",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "3.50",
    "codigo": "ALM-0125"
  },
  {
    "descripcion": "HISOPO DE WC CON BASE",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "8.00",
    "codigo": "ALM-0126"
  },
  {
    "descripcion": "INSECTICIDA",
    "unidad": "FCO",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0127"
  },
  {
    "descripcion": "JABON DE ROPA BOLIVAR",
    "unidad": "1/2 BARR",
    "marca": "BOLIVAR",
    "categoria": "MATERIALES",
    "precio_valorado": "4.00",
    "codigo": "ALM-0128"
  },
  {
    "descripcion": "JABON ESPUMA X 800 ML",
    "unidad": "CJA",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "38.00",
    "codigo": "ALM-0129"
  },
  {
    "descripcion": "JABON LIQUIDO",
    "unidad": "GLN",
    "marca": "COMERCIAL",
    "categoria": "MATERIALES",
    "precio_valorado": "15.00",
    "codigo": "ALM-0130"
  },
  {
    "descripcion": "JABON LIQUIDO",
    "unidad": "LTR",
    "marca": "BALLERINA",
    "categoria": "MATERIALES",
    "precio_valorado": "7.00",
    "codigo": "ALM-0131"
  },
  {
    "descripcion": "JABON LIQUIDO X 500 GR",
    "unidad": "CJA",
    "marca": "KIMBERLY CLARK",
    "categoria": "MATERIALES",
    "precio_valorado": "15.00",
    "codigo": "ALM-0132"
  },
  {
    "descripcion": "JABON LOCION X 800 ML",
    "unidad": "CJA",
    "marca": "SCOTH",
    "categoria": "MATERIALES",
    "precio_valorado": "38.00",
    "codigo": "ALM-0133"
  },
  {
    "descripcion": "JALADOR DE ALUMINIO 1 MTR COMPLETO",
    "unidad": "UND",
    "marca": "EBRIEL",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "95.00",
    "codigo": "ALM-0134"
  },
  {
    "descripcion": "JALADOR DE PISO 1 MTRS - PROFESSIONAL",
    "unidad": "UND",
    "marca": "DARIZA",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "22.00",
    "codigo": "ALM-0135"
  },
  {
    "descripcion": "JALADOR DE PISO 60 CM - MADERA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "25.00",
    "codigo": "ALM-0136"
  },
  {
    "descripcion": "JALADOR DE PISO 60 CM - PVC",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "21.00",
    "codigo": "ALM-0137"
  },
  {
    "descripcion": "JALADOR DE VIDRIOS GRANDE",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "21.00",
    "codigo": "ALM-0138"
  },
  {
    "descripcion": "JALADOR DE VIDRIOS PEQUEÑO 30 CM",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "11.00",
    "codigo": "ALM-0139"
  },
  {
    "descripcion": "KIT SPRAY Y PAÑUELO",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "20.00",
    "codigo": "ALM-0140"
  },
  {
    "descripcion": "LAVA VAJILLA LIQUIDA",
    "unidad": "LTR",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "14.00",
    "codigo": "ALM-0141"
  },
  {
    "descripcion": "LAVA VAJILLA MEDIANO",
    "unidad": "KLG",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "6.50",
    "codigo": "ALM-0142"
  },
  {
    "descripcion": "LAVAVAJILLA PASTA 750 GR",
    "unidad": "UND",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "8.50",
    "codigo": "ALM-0143"
  },
  {
    "descripcion": "LEJIA",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "7.00",
    "codigo": "ALM-0144"
  },
  {
    "descripcion": "LEJIA",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "3.00",
    "codigo": "ALM-0145"
  },
  {
    "descripcion": "LEJIA DE COLOR",
    "unidad": "GLN",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "21.00",
    "codigo": "ALM-0146"
  },
  {
    "descripcion": "LEJIA DE COLOR",
    "unidad": "LTR",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "7.00",
    "codigo": "ALM-0147"
  },
  {
    "descripcion": "LENTES PROTECTORES",
    "unidad": "UND",
    "marca": "INDUSTRIE",
    "categoria": "MATERIALES",
    "precio_valorado": "4.00",
    "codigo": "ALM-0148"
  },
  {
    "descripcion": "LETREROS PISO MOJADO",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "25.00",
    "codigo": "ALM-0149"
  },
  {
    "descripcion": "LIMPIA BRONCE",
    "unidad": "FCO",
    "marca": "BRONCEX",
    "categoria": "MATERIALES",
    "precio_valorado": "11.00",
    "codigo": "ALM-0150"
  },
  {
    "descripcion": "LIMPIA VIDRIO",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "7.50",
    "codigo": "ALM-0151"
  },
  {
    "descripcion": "LIMPIA VIDRIO",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "3.50",
    "codigo": "ALM-0152"
  },
  {
    "descripcion": "LIMPIADOR DE PLATA",
    "unidad": "FCO",
    "marca": "PLATEX",
    "categoria": "MATERIALES",
    "precio_valorado": "13.00",
    "codigo": "ALM-0153"
  },
  {
    "descripcion": "LIQUIDO PARA PISO LAMINADO",
    "unidad": "GALON",
    "marca": "MASTER",
    "categoria": "MATERIALES",
    "precio_valorado": "50.00",
    "codigo": "ALM-0154"
  },
  {
    "descripcion": "MASCARILLA DESCARTABLE CON FILTRO",
    "unidad": "UND",
    "marca": "STEEL PRO",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0155"
  },
  {
    "descripcion": "MASCARILLA KN 95",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "2.00",
    "codigo": "ALM-0156"
  },
  {
    "descripcion": "MASCARILLAS AZULES",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0157"
  },
  {
    "descripcion": "MASCARILLAS DE TELA",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0158"
  },
  {
    "descripcion": "MASCARILLAS PARA POLVO DESCARTABLES",
    "unidad": "CAJAX50",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0159"
  },
  {
    "descripcion": "MASCARILLAS PARA POLVO DESCARTABLES",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "1.00",
    "codigo": "ALM-0160"
  },
  {
    "descripcion": "MECHON 1 KGR",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "15.00",
    "codigo": "ALM-0161"
  },
  {
    "descripcion": "MECHON 350 GR",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "8.00",
    "codigo": "ALM-0162"
  },
  {
    "descripcion": "MECHON 450 GR",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "12.00",
    "codigo": "ALM-0163"
  },
  {
    "descripcion": "MECHON 500 GR",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "14.00",
    "codigo": "ALM-0164"
  },
  {
    "descripcion": "MOOP COMPLETO DE PISO 60 CM (BASE Y MOOP)",
    "unidad": "UND",
    "marca": "EBRIEL",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "42.00",
    "codigo": "ALM-0165"
  },
  {
    "descripcion": "MOPA PERICO COMPLETO",
    "unidad": "UND",
    "marca": "HUDE",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "13.00",
    "codigo": "ALM-0166"
  },
  {
    "descripcion": "PAD PARA MAQUINA BLANCO",
    "unidad": "UND",
    "marca": "3M",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "56.00",
    "codigo": "ALM-0167"
  },
  {
    "descripcion": "PAD PARA MAQUINA CELESTE",
    "unidad": "UND",
    "marca": "3M",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "56.00",
    "codigo": "ALM-0168"
  },
  {
    "descripcion": "PAD PARA MAQUINA ROJO",
    "unidad": "UND",
    "marca": "3M",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "56.00",
    "codigo": "ALM-0169"
  },
  {
    "descripcion": "PAD PARA MAQUINA VERDE",
    "unidad": "UND",
    "marca": "3M",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "56.00",
    "codigo": "ALM-0170"
  },
  {
    "descripcion": "PALO DE MOPA O ESCOBA CON ROSCA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "5.00",
    "codigo": "ALM-0171"
  },
  {
    "descripcion": "PAPEL HIGIENICO ECOLOGICO X 300 MTR",
    "unidad": "ROL",
    "marca": "ELITE",
    "categoria": "MATERIALES",
    "precio_valorado": "22.00",
    "codigo": "ALM-0172"
  },
  {
    "descripcion": "PAPEL HIGIENICO ROLLO X 250 MTR",
    "unidad": "UND",
    "marca": "ELITE",
    "categoria": "MATERIALES",
    "precio_valorado": "12.00",
    "codigo": "ALM-0173"
  },
  {
    "descripcion": "PAPEL HIGIENICO ROLLO X 250 MTR",
    "unidad": "UND",
    "marca": "MISOFI",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0174"
  },
  {
    "descripcion": "PAPEL TOALLA X 200 H",
    "unidad": "PQT",
    "marca": "ARIES",
    "categoria": "MATERIALES",
    "precio_valorado": "25.00",
    "codigo": "ALM-0175"
  },
  {
    "descripcion": "PAPEL TOALLA X 200 H",
    "unidad": "PQT",
    "marca": "ELITE",
    "categoria": "MATERIALES",
    "precio_valorado": "17.00",
    "codigo": "ALM-0176"
  },
  {
    "descripcion": "PAPEL TOALLA X 200 MTRS",
    "unidad": "ROL",
    "marca": "ELITE",
    "categoria": "MATERIALES",
    "precio_valorado": "42.00",
    "codigo": "ALM-0177"
  },
  {
    "descripcion": "PAPEL TOALLA X 200 MTRS",
    "unidad": "ROL",
    "marca": "MISOFI",
    "categoria": "MATERIALES",
    "precio_valorado": "13.80",
    "codigo": "ALM-0178"
  },
  {
    "descripcion": "PAPEL TOALLA X 200 MTRS COLOR NATURAL",
    "unidad": "ROL",
    "marca": "KIMBERLY",
    "categoria": "MATERIALES",
    "precio_valorado": "10.50",
    "codigo": "ALM-0179"
  },
  {
    "descripcion": "PAPEL TOALLA X 300 MTRS",
    "unidad": "ROL",
    "marca": "SUMAC",
    "categoria": "MATERIALES",
    "precio_valorado": "15.00",
    "codigo": "ALM-0180"
  },
  {
    "descripcion": "PASTILLA PARA TANQUE AZUL",
    "unidad": "UND",
    "marca": "PATO TANQUE",
    "categoria": "MATERIALES",
    "precio_valorado": "8.00",
    "codigo": "ALM-0181"
  },
  {
    "descripcion": "PASTILLAS DE BAÑO",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "1.50",
    "codigo": "ALM-0182"
  },
  {
    "descripcion": "PAÑOS ABSORVENTES",
    "unidad": "UND",
    "marca": "SCOTCH BRITE",
    "categoria": "MATERIALES",
    "precio_valorado": "2.60",
    "codigo": "ALM-0183"
  },
  {
    "descripcion": "PAÑOS ABSORVENTES AMARILLOS",
    "unidad": "UND",
    "marca": "VIRUTEX",
    "categoria": "MATERIALES",
    "precio_valorado": "1.50",
    "codigo": "ALM-0184"
  },
  {
    "descripcion": "PAÑOS MICROFIBRA CHICO",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "3.50",
    "codigo": "ALM-0185"
  },
  {
    "descripcion": "PAÑOS MICROFIBRA GRANDE 60 CM",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0186"
  },
  {
    "descripcion": "PAÑUELO DESECHABLE",
    "unidad": "CJA",
    "marca": "KLEENEX",
    "categoria": "MATERIALES",
    "precio_valorado": "9.00",
    "codigo": "ALM-0187"
  },
  {
    "descripcion": "PERFUMATIC X 240 ML",
    "unidad": "CJA",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "11.00",
    "codigo": "ALM-0188"
  },
  {
    "descripcion": "PHILL (PLASTICO ENROLLABLE)",
    "unidad": "ROLLO",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "32.00",
    "codigo": "ALM-0189"
  },
  {
    "descripcion": "PLUMERO DE PLUMAS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "16.00",
    "codigo": "ALM-0190"
  },
  {
    "descripcion": "PORTA MOOP CIRCULAR COMPLETO P/LUNA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "15.00",
    "codigo": "ALM-0191"
  },
  {
    "descripcion": "PORTA MOOP CIRCULAR PARA LUNAS",
    "unidad": "POR DEFINIR",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "8.00",
    "codigo": "ALM-0192"
  },
  {
    "descripcion": "PORTA MOOP PARA PISO (BASE) 60 CM",
    "unidad": "POR DEFINIR",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "24.00",
    "codigo": "ALM-0193"
  },
  {
    "descripcion": "PORTA MOOP PARA PISO 1 MTR (BASE)",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "20.00",
    "codigo": "ALM-0194"
  },
  {
    "descripcion": "PORTA MOOP PARA PISO COMPLETO 1 MTR",
    "unidad": "POR DEFINIR",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "30.00",
    "codigo": "ALM-0195"
  },
  {
    "descripcion": "PORTA MOOP PARA PISO COMPLETO 60 CM BASE Y MOOP",
    "unidad": "UND",
    "marca": "SIN MARCA",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "40.00",
    "codigo": "ALM-0196"
  },
  {
    "descripcion": "PORTA PAD",
    "unidad": "POR DEFINIR",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "135.00",
    "codigo": "ALM-0197"
  },
  {
    "descripcion": "PORTA TRAPEADOR (T DE PALO)",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "7.00",
    "codigo": "ALM-0198"
  },
  {
    "descripcion": "PORTA TRAPEADOR MECHON MANGO DE ALUMINIO DE COLORES",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "23.00",
    "codigo": "ALM-0199"
  },
  {
    "descripcion": "PORTA TRAPEADOR MECHON MANGO DE ALUMINIO DE COLORES - COMPLETO CON MOPA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "23.00",
    "codigo": "ALM-0200"
  },
  {
    "descripcion": "PORTA TRAPEADOR MECHON MANGO DE MADERA DE COLORES",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "25.00",
    "codigo": "ALM-0201"
  },
  {
    "descripcion": "PORTA TRAPEADOR MECHON MANGO DE MADERA DE COLORES -COMPLETO CON MOPA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "40.00",
    "codigo": "ALM-0202"
  },
  {
    "descripcion": "PORTA TRAPEADOR MECHON METAL BASICO C PALO DE MADERA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "90.00",
    "codigo": "ALM-0203"
  },
  {
    "descripcion": "PORTA TRAPEADOR MECHON METAL BASICO C PALO DE MADERA - COMPLETO CON MOPA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "45.00",
    "codigo": "ALM-0204"
  },
  {
    "descripcion": "PREMIO SPRAY",
    "unidad": "FCO",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0205"
  },
  {
    "descripcion": "PRIDE SPRAY",
    "unidad": "FCO",
    "marca": "JHONSON",
    "categoria": "MATERIALES",
    "precio_valorado": "16.00",
    "codigo": "ALM-0206"
  },
  {
    "descripcion": "PULITON",
    "unidad": "KLG",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "5.00",
    "codigo": "ALM-0207"
  },
  {
    "descripcion": "PULVERIZADOR COMPLETO SEGELIM",
    "unidad": "UND",
    "marca": "SEGELIMPERU",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "7.50",
    "codigo": "ALM-0208"
  },
  {
    "descripcion": "QUITA SARRO",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "8.00",
    "codigo": "ALM-0209"
  },
  {
    "descripcion": "QUITA SARRO",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "3.50",
    "codigo": "ALM-0210"
  },
  {
    "descripcion": "RECOGEDOR",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "7.50",
    "codigo": "ALM-0211"
  },
  {
    "descripcion": "RECOGEDOR BAJA POLICIA",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "12.00",
    "codigo": "ALM-0212"
  },
  {
    "descripcion": "REMOVEDOR DE CERA",
    "unidad": "GALON",
    "marca": "BINNER",
    "categoria": "MATERIALES",
    "precio_valorado": "50.00",
    "codigo": "ALM-0213"
  },
  {
    "descripcion": "RENOVADOR DE LLANTAS",
    "unidad": "UND",
    "marca": "SCHUBERT",
    "categoria": "MATERIALES",
    "precio_valorado": "12.00",
    "codigo": "ALM-0214"
  },
  {
    "descripcion": "REPUESTO DE MECHON 450 GR",
    "unidad": "UND",
    "marca": "EBRIEL",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "17.00",
    "codigo": "ALM-0215"
  },
  {
    "descripcion": "REPUESTO DE MOOP CIRCULAR PARA LUNAS",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "8.50",
    "codigo": "ALM-0216"
  },
  {
    "descripcion": "REPUESTO DE MOOP PARA PISO 1 MTR.AZUL",
    "unidad": "POR DEFINIR",
    "marca": "EBRIEL",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "50.00",
    "codigo": "ALM-0217"
  },
  {
    "descripcion": "REPUESTO DE MOOP PARA PISO 1 MTR.BLANCO",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "35.00",
    "codigo": "ALM-0218"
  },
  {
    "descripcion": "REPUESTO DE MOOP PARA PISO 60 CM",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "16.00",
    "codigo": "ALM-0219"
  },
  {
    "descripcion": "REPUESTO DE MOOP PISO MICROFIBRA 0.60 CM",
    "unidad": "UND",
    "marca": "EBRIEL",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "25.00",
    "codigo": "ALM-0220"
  },
  {
    "descripcion": "REPUESTO DE MOOP PISO MICROFIBRA 1.20CM",
    "unidad": "UND",
    "marca": "EBRIEL",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "48.00",
    "codigo": "ALM-0221"
  },
  {
    "descripcion": "REPUESTO DE MOPA PERICO",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "7.50",
    "codigo": "ALM-0222"
  },
  {
    "descripcion": "ROTULADOS PARA BOTELLAS SEGELIM",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "0.65",
    "codigo": "ALM-0223"
  },
  {
    "descripcion": "SACUDIDOR",
    "unidad": "UND",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "4.00",
    "codigo": "ALM-0224"
  },
  {
    "descripcion": "SHAMPOO EN SPRAY",
    "unidad": "UND",
    "marca": "SCHUBERT",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0225"
  },
  {
    "descripcion": "SHAMPOO PARA ALFOMBRAS",
    "unidad": "GLN",
    "marca": "COMERCIAL",
    "categoria": "MATERIALES",
    "precio_valorado": "12.00",
    "codigo": "ALM-0226"
  },
  {
    "descripcion": "SHAMPOO PARA ALFOMBRAS",
    "unidad": "GLN",
    "marca": "DARIZA",
    "categoria": "MATERIALES",
    "precio_valorado": "21.00",
    "codigo": "ALM-0227"
  },
  {
    "descripcion": "SHAMPOO PARA ALFOMBRAS SPRAY",
    "unidad": "FCO",
    "marca": "SCHUBERT",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0228"
  },
  {
    "descripcion": "SHAMPOO PARA AUTOS",
    "unidad": "GLN",
    "marca": "FRESS",
    "categoria": "MATERIALES",
    "precio_valorado": "18.00",
    "codigo": "ALM-0229"
  },
  {
    "descripcion": "SILICONA BLANCA",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "30.00",
    "codigo": "ALM-0230"
  },
  {
    "descripcion": "SILICONA EN SPRAY",
    "unidad": "FCO",
    "marca": "KANUFF",
    "categoria": "MATERIALES",
    "precio_valorado": "10.00",
    "codigo": "ALM-0231"
  },
  {
    "descripcion": "SILICONA EN SPRAY",
    "unidad": "FCO",
    "marca": "SAPOLIO",
    "categoria": "MATERIALES",
    "precio_valorado": "12.00",
    "codigo": "ALM-0232"
  },
  {
    "descripcion": "SILICONA EN SPRAY FRISS",
    "unidad": "FCO",
    "marca": "FRISS",
    "categoria": "MATERIALES",
    "precio_valorado": "23.00",
    "codigo": "ALM-0233"
  },
  {
    "descripcion": "SILICONA LIQUIDA CRISTALINA",
    "unidad": "GLN",
    "marca": "MAFA",
    "categoria": "MATERIALES",
    "precio_valorado": "46.00",
    "codigo": "ALM-0234"
  },
  {
    "descripcion": "SILICONA TRANSPARENTE",
    "unidad": "GLN",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "38.00",
    "codigo": "ALM-0235"
  },
  {
    "descripcion": "SILICONA TRANSPARENTE",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "14.00",
    "codigo": "ALM-0236"
  },
  {
    "descripcion": "SODA CAUSTICA",
    "unidad": "KLG",
    "marca": "",
    "categoria": "IMPLEMENTOS",
    "precio_valorado": "10.00",
    "codigo": "ALM-0237"
  },
  {
    "descripcion": "STAINLESS LIMPIADOR DE ACERO INOX Y MET",
    "unidad": "FCO",
    "marca": "3 M",
    "categoria": "MATERIALES",
    "precio_valorado": "27.00",
    "codigo": "ALM-0238"
  },
  {
    "descripcion": "THINER ACRILICO",
    "unidad": "GLN",
    "marca": "HI LIMP",
    "categoria": "MATERIALES",
    "precio_valorado": "22.00",
    "codigo": "ALM-0239"
  },
  {
    "descripcion": "THINER ACRILICO",
    "unidad": "LTR",
    "marca": "HI LIMP",
    "categoria": "MATERIALES",
    "precio_valorado": "12.00",
    "codigo": "ALM-0240"
  },
  {
    "descripcion": "TOCA",
    "unidad": "CAJA",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "15.00",
    "codigo": "ALM-0241"
  },
  {
    "descripcion": "TRAPEADOR DE FELPA SIMPLE",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "5.00",
    "codigo": "ALM-0242"
  },
  {
    "descripcion": "TRAPEADOR DE MICROFIBRA",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "4.00",
    "codigo": "ALM-0243"
  },
  {
    "descripcion": "TRAPEADOR DE YUTE",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "3.00",
    "codigo": "ALM-0244"
  },
  {
    "descripcion": "TRAPEADOR FELPA DOBLE",
    "unidad": "UND",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "8.00",
    "codigo": "ALM-0245"
  },
  {
    "descripcion": "TRAPO INDUSTRIAL BLANCO",
    "unidad": "KLG",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "5.50",
    "codigo": "ALM-0246"
  },
  {
    "descripcion": "TRAPO INDUSTRIAL DE COLOR",
    "unidad": "KLG",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "5.00",
    "codigo": "ALM-0247"
  },
  {
    "descripcion": "VANISH LIQUIDO X 900 ML",
    "unidad": "FCO",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "10.50",
    "codigo": "ALM-0248"
  },
  {
    "descripcion": "VINAGRE BLANCO",
    "unidad": "LTR",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "6.00",
    "codigo": "ALM-0249"
  },
  {
    "descripcion": "WAYPE CARGADO",
    "unidad": "KLG",
    "marca": "",
    "categoria": "MATERIALES",
    "precio_valorado": "17.00",
    "codigo": "ALM-0250"
  }
]
CATALOGO_JSON
, true);
