# coding: utf-8
from bluepy import btle
import time
import logging
import globals
import struct
from multiconnect import Connector
from notification import Notification

class Lywsd03():
    def __init__(self):
        self.name = 'lywsd03'
        self.ignoreRepeat = False

    def isvalid(self,name,manuf='',data='',mac=''):
        if name.lower() in [self.name]:
            return True
        if data.lower().startswith("95fe") and (mac.lower().startswith("a4:c1:38")):
            #broadcasted advertising data
            return True

    def parse(self,data,mac,name,manuf):
        action = {}
        action['present'] = 1

        if data.lower().startswith("95fe"):
            logging.debug("RAW=" + data)

            for i in range(0, len(data)-4, 2):
                logging.debug(
                    "POS=%02d BYTE=%s" %
                    (i//2, data[i:i+2])
                )

        return action

    def read(self,mac):
        logging.debug('LYWSD03 READ START ' + mac)
        result={}
        try:
            conn = Connector(mac)
            conn.connect()

            if not conn.isconnected:
                conn.connect()
                if not conn.isconnected:
                    return

            batt = bytearray(conn.readCharacteristic('0x37'))
            battery = batt[0]
            logging.debug('LYWSD03 BATTERY=' + str(battery))
            conn.writeCharacteristic('0x35','0100',response=True)
            conn.writeCharacteristic('0x46', 'f40100', response=True)

            notification = Notification(conn, Lywsd03)
            notification.subscribe(10)

            result['battery'] = battery
            result['id'] = mac

            logging.debug('LYWSD03------' + str(result))
            return result

        except Exception as e:
            logging.error('LYWSD03 ERROR : ' + str(e))

        return result


    def handlenotification(self,conn,handle,data,action={}):
        logging.debug(
            'NOTIF handle=%s data=%s'
            % (hex(handle), bytearray(data).hex())
        )

        received = bytearray(data)

        if len(received) >= 3:
            temperature = float(received[1] * 256 + received[0]) / 100
            moisture = received[2]

            logging.debug(
                'TEMP=%s HUM=%s'
                % (temperature, moisture)
            )

            result = {}
            result['temperature'] = temperature
            result['moisture'] = moisture
            result['id'] = conn.mac
            result['source'] = globals.daemonname

            globals.JEEDOM_COM.add_changes(
                'devices::'+conn.mac,
                result
            )

globals.COMPATIBILITY.append(Lywsd03)