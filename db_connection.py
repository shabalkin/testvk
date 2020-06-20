import psycopg2


con = psycopg2.connect(
  database="dc26ecc0mmsvu6", 
  user="vygetfgegbfnef", 
  password="0fcc4db3e52cd342c944f379e803e52dbff287bac895707eebfbab4ff8f7d3ff", 
  host="ec2-3-216-129-140.compute-1.amazonaws.com", 
  port="5432"
)
cur = con.cursor()
def createdb():
    cur.execute('''CREATE TABLE food_giveout_db  
         (
         description_text TEXT ,
         index TEXT ,
         image_url TEXT,
         domain TEXT,
         region TEXT);''')
def insert_in_db(body='',index='',image_url='',domain='',region='',full_link=''):
    sql="insert into food_giveout_db (description_text,index,image_url,domain,region,full_link) values (%s, %s, %s, %s, %s, %s)"
    val=(body,index,image_url,domain,region,full_link)
    cur.execute(sql,val)



def add_column(column='new_column'):
    sql="ALTER TABLE dc26ecc0mmsvu6.food_giveout_db ADD COLUMN %s TEXT "
    val=(column)
    cur.execute("ALTER TABLE food_giveout_db ADD COLUMN full_link TEXT ")


    
#db_connection.con.commit()
#createdb()

#insert_in_db('q','w','e','r','t')
#print("Table created successfully")

