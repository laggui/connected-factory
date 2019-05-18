/*
 * File:   main.c
 * Author: Guillaume Lagrange
 *
 * Created on January 21, 2015, 11:09 AM
 */
#define USE_OR_MASKS
#define _XTAL_FREQ 8000000

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <xc.h>
#include <plib/adc.h>
#include <plib/usart.h>

#define RS232_CONFIG    USART_TX_INT_OFF | USART_RX_INT_OFF | USART_ASYNCH_MODE\
             | USART_EIGHT_BIT | USART_CONT_RX | USART_BRGH_HIGH
#define RS232_SPBRG     207
#define RS232_BAUD      BAUD_16_BIT_RATE | BAUD_AUTO_OFF | BAUD_IDLE_CLK_LOW

#pragma config  OSC = INTIO67, BOREN = OFF, PWRT = ON, WDT = OFF, DEBUG = ON, LVP = OFF

#define DEVICE_ADDR "2"
#define SOP         "<" //Start of packet
#define EOP         ">" //End of packet
#define PAD         "-" //Packet (device) address delimiter
#define NULL_CHAR   "\0"

//TMR0 period = 0.5uS --> 0.5uS * 256 (prescaler) = 128uS
//128uS * 256 = 0.032768 = overflow period
// 5/0.032768 = 152.587 = overflow counter
#define OVERFLOW_COUNT  153

unsigned int result = 0;
char adc_buffer[5];
char data_buffer[10];
int cnt = 0;
int send_data = 0;

void interrupt ISR() {
    if (INTCONbits.TMR0IF == 1) {
        cnt++;
        if (cnt == OVERFLOW_COUNT) {
            cnt = 0;
            send_data = 1; //send data every 5 sec
        }
        INTCONbits.TMR0IF = 0;
    }
}

int main(int argc, char** argv) {
    OSCCON = 0b11110111; //8MHz int. osc.
    ADCON1 = 0x00001110; //AN0 as analog input

    T0CON = 0x47; //TMR0 8-bit, 256 prescaler    
    INTCON = 0xE0; //Enable GIE and TMR0IE
    //TMR0 freq = 8MHz/4/256/256 = 30.517 Hz --> TMR0 interrupt every 0.032768 sec

    CloseUSART();
    CloseADC();

    unsigned char config, config2, portconfig;
    config = ADC_FOSC_2 | ADC_RIGHT_JUST | ADC_2_TAD;
    config2 = ADC_CH0 | ADC_INT_OFF | ADC_REF_VDD_VSS;
    portconfig = ADC_0ANA;

    OpenUSART(RS232_CONFIG, RS232_SPBRG);
    baudUSART(RS232_BAUD);
    OpenADC(config, config2, portconfig);
    T0CONbits.TMR0ON = 1; //Activate TMR0
    //TRISBbits.TRISB0 = 0;
    while (1) {
        if (send_data == 1) {

            ConvertADC();
            while(BusyADC()); //wait for the conversion to end
            result = (ADRESH<<8)+ADRESL; //combine the 10 bits of the conversion
            sprintf(adc_buffer, "%d", result);
            strcpy(data_buffer, SOP);
            strcat(data_buffer, DEVICE_ADDR);
            strcat(data_buffer, PAD);
            strcat(data_buffer, adc_buffer);
            strcat(data_buffer, EOP);
            strcat(data_buffer, NULL_CHAR);
            while(BusyUSART());
            putsUSART((char *)data_buffer);
            __delay_ms(25);       
            send_data = 0;

        }
    }
    return (EXIT_SUCCESS);
}