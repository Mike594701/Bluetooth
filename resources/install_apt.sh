#!/bin/bash

PROGRESS_FILE=/tmp/dependancy_bluetooth_in_progress

if [ ! -z "$1" ]; then
    PROGRESS_FILE=$1
fi

touch ${PROGRESS_FILE}

echo 0 > ${PROGRESS_FILE}

echo "********************************************************"
echo "*         Installation des dépendances bluetooth           *"
echo "********************************************************"

sudo apt-get update

echo 20 > ${PROGRESS_FILE}

sudo apt-get install -y \
python3 \
python3-dev \
python3-pip \
python3-setuptools \
python3-wheel \
python3-requests \
python3-serial \
python3-pyudev \
python3-cryptography \
build-essential \
bluetooth \
bluez \
libffi-dev \
libssl-dev \
libbluetooth-dev \
libglib2.0-dev \
libopenjp2-7 \
libtiff6 \
libatlas-base-dev \
git \
rfkill

echo 40 > ${PROGRESS_FILE}

sudo pip3 install --break-system-packages wheel
sudo pip3 install --break-system-packages -U setuptools

echo 50 > ${PROGRESS_FILE}

sudo pip3 install --break-system-packages pyudev
sudo pip3 install --break-system-packages pyserial
sudo pip3 install --break-system-packages requests
sudo pip3 install --break-system-packages pybluez
sudo pip3 install --break-system-packages pillow
sudo pip3 install --break-system-packages numpy
sudo pip3 install --break-system-packages cryptography
sudo pip3 install --break-system-packages pycryptodomex
sudo pip3 install --break-system-packages bluepy

echo 60 > ${PROGRESS_FILE}

cd /tmp

sudo rm -rf /tmp/bluepy

sudo git clone https://github.com/IanHarvey/bluepy.git

cd /tmp/bluepy

sudo python3 setup.py build
sudo python3 setup.py install

sudo rm -rf /tmp/bluepy

echo 80 > ${PROGRESS_FILE}

sudo systemctl restart bluetooth

sudo rfkill unblock all >/dev/null 2>&1

sudo hciconfig hci0 up >/dev/null 2>&1
sudo hciconfig hci1 up >/dev/null 2>&1
sudo hciconfig hci2 up >/dev/null 2>&1

echo 90 > ${PROGRESS_FILE}

python3 -c "import pyudev"
python3 -c "import serial"
python3 -c "from bluepy.btle import Scanner"

echo 100 > ${PROGRESS_FILE}

echo "********************************************************"
echo "*          Installation terminée                       *"
echo "********************************************************"

rm -f ${PROGRESS_FILE}

exit 0