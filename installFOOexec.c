#include <stdlib.h>
#include <stdio.h>
#include <sys/types.h>
#include <unistd.h>
#include <string.h>

int main (int argc, char *argv[])
{
  setuid (0);  
  if (argc == 2){
    char cmd[255];
    strcpy (cmd, "/bin/sh /path/to/script/installFOO.sh ");
    strcat (cmd, argv[1]);
    system(cmd);
  }
  return 0;
}
