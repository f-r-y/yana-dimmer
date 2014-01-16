yana-dimmer
============

Plugin de variateur via pi-blaster (permet de dimmer des led en pwm, ou plus costaud via les composants adaptés)
 https://github.com/sarfata/pi-blaster suivre le readme pour installer
 
 /!\ pour les possesseurs de rpi rev 2 il faut editer le fichier pi-blaster.c et remplacer '21' par '27' a la ligne 45 avant de compiler (la numérotation des GPIO a changé enntre les deux révisions)

/!\ l'installation de piblaster empeche le fonctionnement normal (quand le daemon tourne) des gpio utilisés par la lib (4, 17, 18, 21, 22, 23, 24 et 25 sur la raspberry-leaf http://www.doctormonk.com/2013/02/raspberry-pi-and-breadboard-raspberry.html , si vous avez un relais utilisant un de ces pins, le module "relay" ne permetra pas d'activer ou de desactiver le relai (mais le module variateur oui tant que la valeur sera 0 ou 1, le relai risque de pas aimer "0.5" ce qui le ferait changer d'etat un tres grand nombre de fois par seconde)
