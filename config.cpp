#include "config.h"
#include <iostream>
#include <fstream>
#include <string>

using namespace std;

int Config::load(const char* file)
{
    ofstream cfgFile;
    cfgFile.open (file);
    cfgFile << "Writing this to a file.\n";
    cfgFile.close();
    return 0;
}

Php::Value Config::inc(Php::Parameters &params)
{ 
    return _val += params.size() > 0 ? (int) params[0] : 1;
}

Php::Value Config::dec(Php::Parameters &params)
{ 
    return _val -= params.size() > 0 ? (int) params[0] : 1;
}

Php::Value Config::getVal()
{
    return _val;
}
