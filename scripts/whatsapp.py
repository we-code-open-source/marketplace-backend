import sys
import getopt
import json


try:

    import pywhatkit

    def get_arguments():

        # try:
        argument = sys.argv[1:]

        short_options = "p:m:h"
        long_options = ["phone=", "message=", "help"]

        arguments, values = getopt.getopt(
            argument,
            short_options,
            long_options
        )
        return arguments

        # except getopt.error as err:
        #    # Output error, and return with an error code
        #    print(str(err))
        #    sys.exit(2)

    def get_arguments_as_dict():
        args = get_arguments()
        args_dict = {}

        for argument, value in args:
            args_dict[argument] = value

        return args_dict

    def check_arguments_exists(args, arguments):
        for a in arguments:
            if not args.get(a, False):
                raise Exception(f"Argument ({a}) is required but not sent")

    args = get_arguments_as_dict()
    check_arguments_exists(args, ['--phone', '--message'])
    pywhatkit.sendwhatmsg_instantly(args['--phone'], args['--message'])
    print(json.dumps({
        "success": True,
        "message": "It should be sent"
    }))
except BaseException as e:
    print(json.dumps({
        "success": False,
        "message": str(e)
    }))
