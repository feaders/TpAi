from bs4 import BeautifulSoup
import requests
import numpy
import sys


class Phrase:
    def __init__(self, position, texte, importance):
        self.position = position
        self.texte = texte
        self.importance = importance

def init_url(url_site):
    # Récupérer le contenu de la page
    urlContent = requests.get(url_site)
    page = urlContent.content

    # Utiliser le package sur la page
    soup = BeautifulSoup(page, 'html5lib')
    
    return soup

def get_texteArticle(soup):
    texte_article = ''
    liste_paragraphes = soup.find_all('p', class_='article__paragraph')
    for paragraphe in liste_paragraphes:
        texte_article += paragraphe.get_text()

    return texte_article

def recup_motsOccurence(texte_article_modifie):
    mots = texte_article_modifie.split()
    occurences= dict()
    for m in mots:
        if m in occurences.keys():
            occurences[m]+=1
        else:
            occurences[m]=1

    return occurences
    
def tri_motsImportance(occurences):
    sorted_dict = {}
    sorted_keys = sorted(occurences, key=occurences.get, reverse=True)
    for w in sorted_keys:
        sorted_dict[w] = occurences[w]

    return sorted_dict

def selection_motsAutorises(sorted_dict):
    liste_final = dict()
    for m in sorted_dict:
      if estImportant(m):
        liste_final[m]= sorted_dict[m]

    return liste_final

def estImportant(m):
    f = open('mots.txt')
    motsBannis=f.read().split(',')
    return numpy.invert(m in motsBannis)

def importance(phrase,liste_final): 
    freq=0
    mots = phrase.texte.split()
    for m in mots:
        if m in liste_final:
            freq += 1
        phrase.importance = freq

def generer_resume(url_site):
    # Init Beautiful Soup
    soup = init_url(url_site)

    # Récupérer le contenu de la page
    texte_article = get_texteArticle(soup)

    # Reduction du contenu du texte
    texte_article_modifie = texte_article
    texte_article_modifie = texte_article_modifie.lower()
    texte_article_modifie = texte_article_modifie.replace("<stop>", '').replace('.', '').replace(',', '').replace(';', '').replace(":", '').replace("(", '').replace(")", '')
    texte_article_modifie = texte_article_modifie.replace('?', '').replace('!', '').replace("'", ' ').replace("’", ' ').replace(".<stop>", '').replace("»", '')

    # Recuperation des occurences des mots 
    occurences = recup_motsOccurence(texte_article_modifie)
    # Tri par importance
    sorted_dict = tri_motsImportance(occurences)

    # Selection avec suppression des mots bannis
    liste_final = selection_motsAutorises(sorted_dict)

    # Recuperation des phrases
    texte_article = texte_article.replace(".",".<stop>")
    texte_article = texte_article.replace("?","?<stop>")
    texte_article = texte_article.replace("!","!<stop>")
    phrases = texte_article.split('<stop>')

    liste_phrase = []
    compteur = 0
    for phrase in phrases:
        liste_phrase.append(Phrase(compteur, phrase, 0))
        compteur += 1

    #

    for phrase in liste_phrase:
        importance(phrase,liste_final)

    #

    phrases_final = liste_phrase.copy()
    phrases_final.sort(key=lambda x: x.importance, reverse=True)
    txt= ""
    i=0
    for phrase in phrases_final:
        txt+= phrase.texte
        i+=1
        if i/ len(phrases_final) > 0.33:
            break

    compteur = 0
    liste_freq = []
    for mot in liste_final:
        liste_freq.append(str(mot) + ' : ' + str(liste_final[mot]) )
        if compteur < 9:
            compteur += 1
        else:
            break
    freq = ', '.join(liste_freq)
    jsonResultat = '{"resume":"'+ txt +'","freq":"' + freq + '"}'

    return jsonResultat

site = "https://www.lemonde.fr/sciences/article/2020/11/30/covid-19-la-saga-du-vaccin-a-arn-messager-dans-le-sprint-final_6061695_1650684.html"
print(generer_resume(sys.argv[1]))
