#include <phpcpp.h>
#include <iostream>
#include <fstream>
#include <string>

using namespace std;

class Config: public Php::Base
{
    private:
        int _val = 0;
        /* load configure file */
        int load(const char* file);

    public:
        Config(Php::Parameters &params)
        {
            if (params.size() == 0)
                return;
            cout << params[0] << endl;
            this->load((const char *)params[0]);
            
        }

        virtual ~Config() {}
        
        Php::Value inc(Php::Parameters &params);
        Php::Value dec(Php::Parameters &params);
        Php::Value getVal();
};
