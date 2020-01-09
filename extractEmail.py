import re

def extract_email(email):
    email = open(email, "r")
    email = email.read()
    email = re.findall(r"([^@|\s]+@[^@]+\.[^@|\s]+)", email)
    if email:
        try:
            return email[0].split()[0].strip(';')
        except Exception as e:
            return e
			

addr = extract_email("output.txt")
print(addr)