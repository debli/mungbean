#include <phpcpp.h>

class Config: public Php::Base
{
    private:
        int _val = 0;

    public:
        Config() {}
        virtual ~Config() {}
        
        Php::Value inc(Php::Parameters &params);
        Php::Value dec(Php::Parameters &params);
        Php::Value getVal();
};
