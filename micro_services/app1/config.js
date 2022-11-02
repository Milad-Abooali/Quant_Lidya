const process = require( 'process' );
const argv = key => {
	// Return true if the key exists and a value is defined
	if ( process.argv.includes( `--${ key }` ) ) return true;

	const value = process.argv.find( element => element.startsWith( `--${ key }=` ) );

	// Return null if the key does not exist and a value is not defined
	if ( !value ) return null;

	return value.replace( `--${ key }=` , '' );
}

const isDevEnv = (argv('devEnv')) ? true : false;

module.exports = {
	argv    : argv,
	isDevEnv: isDevEnv,
	ssl		: true,
	appPath : __dirname, /* '/home/lidyapartners/public_html/lidyacrm/app/socket' */
	appUrl  : (isDevEnv) ? 'https://crmlab' : 'https://clientzone2.lidyaportal.com',
	appHost : (isDevEnv) ? 'crmlab' : 'clientzone2.lidyaportal.com'
};