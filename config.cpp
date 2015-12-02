#include "config.h"

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
