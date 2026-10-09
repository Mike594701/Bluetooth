#!/bin/bash

touch /tmp/dependancy_blea_in_progress
echo 0 > /tmp/dependancy_blea_in_progress

echo "********************************************************"
echo "*         Installation des dépendances BLEA           *"
echo "********************************************************"

sudo apt-get update

echo 20 > /tmp/dependancy_blea_in_progress

sudo apt-get install -y \
bluetooth \
bluez \
python3 \
python3-pip \
python3-dev \
python3-setuptools \
python3-wheel \
python3-requests \
python3-cryptography \
python3-serial \
build-essential \
git \
libffi-dev \
libssl-dev \
libglib2.0-dev

echo 50 > /tmp/dependancy_blea_in_progress

# Debian 12 (PEP668)
sudo pip3 install --break-system-packages pyudev
sudo pip3 install --break-system-packages pyserial
sudo pip3 install --break-system-packages requests
sudo pip3 install --break-system-packages cryptography
sudo pip3 install --break-system-packages pycryptodomex

echo 70 > /tmp/dependancy_blea_in_progress

cd /tmp

sudo rm -rf bluepy

sudo git clone https://github.com/sarakha63/bluepy.git

cd bluepy

sudo python3 setup.py build
sudo python3 setup.py install

echo 90 > /tmp/dependancy_blea_in_progress

sudo systemctl restart bluetooth

sudo hciconfig hci0 up >/dev/null 2>&1
sudo hciconfig hci1 up >/dev/null 2>&1
sudo hciconfig hci2 up >/dev/null 2>&1

sudo rm -rf /tmp/bluepy

echo 100 > /tmp/dependancy_blea_in_progress

echo "Everything is successfully installed!"

rm -f /tmp/dependancy_blea_in_progress
``