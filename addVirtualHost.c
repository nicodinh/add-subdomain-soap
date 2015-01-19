#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <unistd.h>
#include <string.h>

int main (int argc, char *argv[])
{
	setuid (0); 
	// step
	// 0. 3 arguments : "/etc/apache2/sites-available/foo", "foo.domain.com", "foo"
	// 1. create virtualhost file "foo.domain.com" into "/etc/apache2/sites-available/"
	// 2. system(a2ensite foo);
	// 3. system(service apache2 reload);
	if (argc == 4)
	{
		char cmd1[250];
		FILE *file = NULL;
		file = fopen(argv[1], "w");
		if (file != NULL)
		{
			fprintf(file, "<VirtualHost *:80>\n\tServerAdmin abuse@domain.com\n\tServerName %s.domain.com\n\tDocumentRoot /path/to/www/%s/\n\t<Directory /path/to/www/%s/>\n\t\tOptions -Indexes FollowSymLinks MultiViews\n\t\tAllowOverride All\n\t</Directory>\n\tServerSignature Off\n\tErrorLog ${APACHE_LOG_DIR}/error_%s_domain_com.log\n\tLogLevel warn\n\tCustomLog ${APACHE_LOG_DIR}/access_%s_domain_com.log combined\n</VirtualHost>", argv[3], argv[3], argv[3], argv[3], argv[3]);
			fclose(file);
			strcpy(cmd1, "/usr/sbin/a2ensite");
			strcat(cmd1, " ");
			strcat(cmd1, argv[2]);
			system(cmd1);
			system("/usr/sbin/service apache2 reload");
		}
	}
	return 0;
}
