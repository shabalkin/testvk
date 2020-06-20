import requests
import json
import io
import csv
import psycopg2
import db_connection

id_to_domain_tuple={'-70298501':'foodsharing_spb','-161997638':'club_helpfoodspb','-117995648':'foodsharing_piter'
                    
                    
                    }
rnd_domains=['fsh_rnd','foodsharingrnd']
msc_domains=['foodsharing_msk','food_sharing']
spb_domains=['foodsharing_spb','club_helpfoodspb','foodsharing_piter']
regions=[#'rnd',
         #'msc',
         'spb'
         ]
version=5.29    
access_token='937ef3ab7ee4280027008cfda9c8b3a3109960ea99f5ba1dbbe002660b46dc3c87ecd4928ba9775e40750'

def main():
    for region in regions:        
        all_data=[]
    
        if region=='rnd':
            domain_pool=rnd_domains
        elif region=='msc':
            domain_pool=msc_domains
        elif region=='spb':
            domain_pool=spb_domains
        for domain in domain_pool:    
            offset=0
            while offset < 20:
                r = requests.get('https://api.vk.com/method/wall.get', params={        
                        'access_token':access_token,
                        'domain': domain,
                       'count' : 1,
                       'offset':offset,
                       'v':version
                   })
            
            
                try:
                    data=r.json()['response']['items'] 
                    data[-1].update({'region':region})
                    print(data)
                    offset=offset+1
                    all_data.extend(data)
                except:
                    pass
            
            

    with open('posts_file.json','w',encoding='utf8') as file:
        json.dump(all_data,file,indent=2,ensure_ascii=False)
        
    
    return all_data

def write(data):
    
    with open('needable_data.csv','w',encoding='utf8',newline='') as file:
        a_pen=csv.writer(file)
        a_pen.writerow(('body','index','image_url','domain','region'))
        for posts in data:  #проверяем наличие прикрепленных картинок, по возможности сохраняем их адрес
            try:
                if posts['attachments'][0]['type']:
                    img_url=posts['attachments'][0]['photo']["photo_604"]
                else:
                    img_url ='pass'
            except:
                img_url ='pass'
            

            domain=id_to_domain_tuple.get(str(posts['owner_id'])) #возвращает домен по циферному айди группы
            
            db_connection.insert_in_db(posts['text'],posts['id'],img_url,domain,posts['region'],
                                       full_link_generate(domain,posts['owner_id'],posts['id']))
            db_connection.con.commit() 
            
            #a_pen.writerow((posts['text'],posts['id'],img_url,domain,posts['region'],
                                       #full_link_generate(domain,posts['owner_id'],posts['id'])))
                
            
                            

 
def full_link_generate(group_domain,group_id,index):
    full_link='https://vk.com/'+str(group_domain)+'?w=wall'+str(group_id)+'_'+str(index)
    return full_link
all_posts=main()
write(all_posts)
db_connection.con.close()






